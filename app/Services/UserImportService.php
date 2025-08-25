<?php


namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use League\Csv\Reader;
use Illuminate\Http\UploadedFile;

class UserImportService
{
    protected $organization;
    protected $errors = [];
    protected $successCount = 0;

    
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function importFromCsv(UploadedFile $file, array $options = [])
    {
        try {
            // Check if file is valid
            if (!$file->isValid()) {
                $this->errors[] = "Invalid file upload";
                return false;
            }

            // Get file extension
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'xlsx' || $extension === 'xls') {
                return $this->importFromExcel($file, $options);
            }
            
            return $this->importFromCsvFile($file, $options);

        } catch (\Exception $e) {
            $this->errors[] = "File processing error: " . $e->getMessage();
            return false;
        }
    }

    protected function importFromCsvFile(UploadedFile $file, array $options)
    {
        try {
            $csv = Reader::createFromPath($file->getRealPath());
            $csv->setHeaderOffset(0);
            
            $headers = $csv->getHeader();
            if (!in_array('name', $headers) || !in_array('email', $headers)) {
                $this->errors[] = "CSV must contain 'name' and 'email' columns";
                return false;
            }

            $records = $csv->getRecords();
            $users = [];

            foreach ($records as $index => $record) {
                $rowNumber = $index + 2;
                
                // Skip empty rows
                if (empty($record['name']) && empty($record['email'])) {
                    continue;
                }

                // Validate required fields
                if (empty($record['name'])) {
                    $this->errors[] = "Row {$rowNumber}: Name is required";
                    continue;
                }

                if (empty($record['email'])) {
                    $this->errors[] = "Row {$rowNumber}: Email is required";
                    continue;
                }

                // Validate email format
                if (!filter_var(trim($record['email']), FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = "Row {$rowNumber}: Invalid email format - {$record['email']}";
                    continue;
                }

                $email = trim($record['email']);

                // Check if user already exists globally
                $existingUser = User::where('email', $email)->first();
                if ($existingUser) {
                    // Check if user is already in organization
                    $inOrganization = $this->organization->members()
                        ->where('email', $email)
                        ->exists();
                    
                    if ($inOrganization) {
                        $this->errors[] = "Row {$rowNumber}: User with email {$email} already exists in organization";
                        continue;
                    }
                }

                $users[] = [
                    'name' => trim($record['name']),
                    'email' => $email,
                    'password' => Hash::make($this->generatePassword(trim($record['name']))),
                    'is_active' => true,
                    'timezone' => $options['timezone'] ?? config('app.timezone'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($users)) {
                $this->errors[] = "No valid users found in the file";
                return false;
            }

            return $this->createUsers($users);

        } catch (\Exception $e) {
            $this->errors[] = "CSV processing error: " . $e->getMessage();
            return false;
        }
    }

    protected function importFromExcel(UploadedFile $file, array $options)
    {
        try {
            // Load the Excel file using PhpSpreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            if (count($data) < 2) {
                $this->errors[] = "Excel file must contain at least a header row and one data row";
                return false;
            }
    
            // Get headers from first row
            $headers = array_map(function($value) {
                return is_string($value) ? strtolower(trim($value)) : '';
            }, $data[0]);
            
            \Log::info('Excel headers found:', $headers);
    
            // Find column indices
            $nameIndex = $this->findColumnIndex($headers, ['name', 'fullname', 'full name', 'user name']);
            $emailIndex = $this->findColumnIndex($headers, ['email', 'email address', 'e-mail', 'e-mail address']);
    
            if ($nameIndex === false) {
                $this->errors[] = "Excel must contain a 'name' column. Found columns: " . implode(', ', $data[0]);
                return false;
            }
    
            if ($emailIndex === false) {
                $this->errors[] = "Excel must contain an 'email' column. Found columns: " . implode(', ', $data[0]);
                return false;
            }
    
            $users = [];
            
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];
                $rowNumber = $i + 1;
                
                if (count($row) <= max($nameIndex, $emailIndex)) {
                    $this->errors[] = "Row {$rowNumber}: Incomplete row data";
                    continue;
                }
    
                $name = is_string($row[$nameIndex] ?? '') ? trim($row[$nameIndex]) : '';
                $email = is_string($row[$emailIndex] ?? '') ? trim($row[$emailIndex]) : '';
    
                // Skip empty rows
                if (empty($name) && empty($email)) {
                    continue;
                }
    
                // Validate required fields
                if (empty($name)) {
                    $this->errors[] = "Row {$rowNumber}: Name is required";
                    continue;
                }
    
                if (empty($email)) {
                    $this->errors[] = "Row {$rowNumber}: Email is required";
                    continue;
                }
    
                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = "Row {$rowNumber}: Invalid email format - {$email}";
                    continue;
                }
    
                // Check if user already exists
                $existingUser = User::where('email', $email)->first();
                if ($existingUser) {
                    $inOrganization = $this->organization->members()
                        ->where('email', $email)
                        ->exists();
                    
                    if ($inOrganization) {
                        $this->errors[] = "Row {$rowNumber}: User with email {$email} already exists in organization";
                        continue;
                    }
                }
    
                $users[] = [
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($this->generatePassword($name)),
                    'is_active' => true,
                    'timezone' => $options['timezone'] ?? config('app.timezone'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            if (empty($users)) {
                $this->errors[] = "No valid users found in the file";
                return false;
            }
    
            return $this->createUsers($users);
    
        } catch (\Exception $e) {
            $this->errors[] = "Excel processing error: " . $e->getMessage();
            \Log::error('Excel import error: ' . $e->getMessage());
            return false;
        }
    }

    protected function createUsers(array $users)
    {
        try {
            DB::beginTransaction();

            foreach ($users as $userData) {
                try {
                    // Check if user exists globally but not in organization
                    $user = User::where('email', $userData['email'])->first();
                    
                    if (!$user) {
                        $user = User::create($userData);
                    }
                    
                    // Assign examinee role
                    if (!$user->hasRole('examinee')) {
                        $user->assignRole('examinee');
                    }
                    
                    // Add to organization if not already member
                    if (!$this->organization->members()->where('user_id', $user->id)->exists()) {
                        $this->organization->members()->attach($user->id, [
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->errors[] = "Failed to create user {$userData['email']}: " . $e->getMessage();
                    DB::rollBack();
                    return false;
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = "Database transaction error: " . $e->getMessage();
            return false;
        }
    }


    protected function generatePassword($name)
    {
        // Generate password from name: first 4 letters + random 4 digits
        $namePart = Str::lower(preg_replace('/[^a-zA-Z]/', '', $name));
        //$namePart = Str::lower(Str::substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 4));
        //$numberPart = rand(1000, 9999);
        
        return $namePart;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getResults()
    {
        return [
            'success_count' => $this->successCount,
            'error_count' => count($this->errors),
            'errors' => $this->errors
        ];
    }
}