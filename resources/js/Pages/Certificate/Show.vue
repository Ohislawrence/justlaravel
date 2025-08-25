<template>
    <AppLayout title="View Certificate">
      <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-white shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
              <h2 class="text-xl font-semibold">Certificate Details</h2>
              <div class="space-x-2">
                <button @click="downloadPdf" class="btn btn-primary">
                  Download PDF
                </button>
                <Link :href="route('certificates.index')" class="btn btn-secondary">
                  Back to List
                </Link>
              </div>
            </div>
          </div>
  
          <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <!-- Certificate Display -->
            <div class="certificate-container" ref="certificateContainer">
              <div class="certificate-content" v-html="certificate.content"></div>
            </div>
  
            <!-- Certificate Details -->
            <div class="p-6 border-t border-gray-200">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 class="text-lg font-medium mb-4">Certificate Information</h3>
                  <dl class="grid grid-cols-2 gap-y-2">
                    <dt class="font-medium">Recipient:</dt>
                    <dd>{{ certificate.user.name }}</dd>
                    <dt class="font-medium">Email:</dt>
                    <dd>{{ certificate.user.email }}</dd>
                    <dt class="font-medium">Quiz:</dt>
                    <dd>{{ certificate.quiz.title }}</dd>
                    <dt class="font-medium">Score:</dt>
                    <dd>{{ certificate.metadata.score }}%</dd>
                    <dt class="font-medium">Issued On:</dt>
                    <dd>{{ formatDate(certificate.issued_at) }}</dd>
                    <dt class="font-medium">Expires On:</dt>
                    <dd>{{ certificate.expires_at ? formatDate(certificate.expires_at) : 'Never' }}</dd>
                  </dl>
                </div>
                <div>
                  <h3 class="text-lg font-medium mb-4">Verification</h3>
                  <div class="flex items-start">
                    <div class="mr-4">
                      <div ref="qrCode" class="w-32 h-32 bg-white p-2 border"></div>
                    </div>
                    <div>
                      <p class="mb-2">Certificate ID: <strong>{{ certificate.certificate_number }}</strong></p>
                      <p class="text-sm text-gray-600">Scan the QR code or visit the verification page to validate this certificate.</p>
                      <p class="text-sm mt-2">
                        <a :href="verificationUrl" class="text-blue-600 hover:underline" target="_blank">
                          {{ verificationUrl }}
                        </a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import QRCode from 'qrcode';
  import { usePage } from '@inertiajs/vue3';
  import html2pdf from 'html2pdf.js';
  
  const props = defineProps({
    certificate: {
      type: Object,
      required: true
    }
  });
  
  const certificateContainer = ref(null);
  const qrCode = ref(null);
  const verificationUrl = ref('');
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };
  
  const downloadPdf = () => {
    const element = certificateContainer.value;
    const opt = {
      margin: 10,
      filename: `${props.certificate.quiz.title}_certificate_${props.certificate.user.name}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
  
    html2pdf().from(element).set(opt).save();
  };
  
  onMounted(() => {
    // Generate verification URL
    const url = new URL(route('certificates.verify'), window.location.origin);
    url.searchParams.append('id', props.certificate.id);
    verificationUrl.value = url.toString();
  
    // Generate QR code
    QRCode.toCanvas(qrCode.value, verificationUrl.value, { width: 120 }, (error) => {
      if (error) console.error(error);
    });
  });
  </script>
  
  <style scoped>
  .certificate-container {
    width: 100%;
    background-color: white;
    position: relative;
  }
  
  .certificate-content {
    width: 100%;
    min-height: 500px;
    border: 1px solid #e5e7eb;
  }
  
  @media print {
    .certificate-container {
      width: 297mm;
      height: 210mm;
    }
    
    .certificate-content {
      width: 100%;
      height: 100%;
      border: none;
    }
  }
  </style>