<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class DeviceLocationService
{
    protected $request;
    protected $crawlerDetect;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->crawlerDetect = new CrawlerDetect();
    }

    /**
     * Get all device and location information
     */
    public function getDeviceLocationData()
    {
        $locationData = $this->getLocationData();
        
        return [
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'device_type' => $this->getDeviceType(),
            'browser' => $this->getBrowser(),
            'platform' => $this->getPlatform(),
            'location_data' => json_encode($locationData), 
            'country' => $this->getCountry(),
            'city' => $this->getCity(),
            'region' => $this->getRegion(),
        ];
    }

    /**
     * Get client IP address
     */
    public function getIpAddress()
    {
        return $this->request->ip();
    }

    /**
     * Get user agent
     */
    public function getUserAgent()
    {
        return $this->request->userAgent();
    }

    /**
     * Detect device type
     */
    public function getDeviceType()
    {
        $userAgent = $this->getUserAgent();
        
        if (preg_match('/(mobile|android|iphone|ipod|blackberry|opera mini)/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/(tablet|ipad|playbook|kindle)/i', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Detect browser
     */
    public function getBrowser()
    {
        $userAgent = $this->getUserAgent();
        
        if (preg_match('/chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/edge/i', $userAgent)) {
            return 'Edge';
        } elseif (preg_match('/opera/i', $userAgent)) {
            return 'Opera';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Detect platform/OS
     */
    public function getPlatform()
    {
        $userAgent = $this->getUserAgent();
        
        if (preg_match('/windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iphone|ipad/i', $userAgent)) {
            return 'iOS';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Get location data from IP address
     */
    public function getLocationData()
    {
        $ip = $this->getIpAddress();
        
        // Skip for local IPs
        if ($this->isLocalIp($ip)) {
            return ['local' => true];
        }

        try {
            // Using ipapi.co (free tier available)
            $response = Http::timeout(3)->get("http://ipapi.co/{$ip}/json/");
            
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Fallback to a different service
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'ip' => $data['query'] ?? $ip,
                        'country' => $data['country'] ?? null,
                        'country_code' => $data['countryCode'] ?? null,
                        'region' => $data['regionName'] ?? null,
                        'city' => $data['city'] ?? null,
                        'zip' => $data['zip'] ?? null,
                        'lat' => $data['lat'] ?? null,
                        'lon' => $data['lon'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'isp' => $data['isp'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                return ['error' => 'Unable to fetch location data'];
            }
        }

        return [];
    }

    /**
     * Get country from location data
     */
    public function getCountry()
    {
        $locationData = $this->getLocationData();
        return $locationData['country'] ?? $locationData['country_name'] ?? null;
    }

    /**
     * Get city from location data
     */
    public function getCity()
    {
        $locationData = $this->getLocationData();
        return $locationData['city'] ?? null;
    }

    /**
     * Get region from location data
     */
    public function getRegion()
    {
        $locationData = $this->getLocationData();
        return $locationData['region'] ?? $locationData['region_name'] ?? $locationData['state'] ?? null;
    }

    /**
     * Check if IP is local
     */
    private function isLocalIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}