{{-- Composant : upload photo + capture webcam --}}
@props(['name' => 'photo', 'label' => "Photo", 'currentUrl' => null, 'required' => false])

<div x-data="webcamPhoto('{{ $name }}')" x-init="init()">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>

    {{-- Prévisualisation --}}
    <div class="flex items-start gap-4 mb-3">
        <div class="relative flex-shrink-0">
            <img :src="previewUrl || '{{ $currentUrl ?? '' }}'"
                 x-show="previewUrl || '{{ $currentUrl }}'"
                 class="w-20 h-20 rounded-xl object-cover border-2 border-blue-200 shadow-sm"
                 onerror="this.style.display='none'">
            <div x-show="!previewUrl && !{{ $currentUrl ? 'true' : 'false' }}"
                 class="w-20 h-20 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>

        <div class="flex-1 space-y-2">
            {{-- Champ fichier --}}
            <div>
                <label class="block text-xs text-gray-500 mb-1">Choisir un fichier</label>
                <input type="file" name="{{ $name }}" id="fileInput_{{ $name }}"
                       accept="image/jpeg,image/png,image/jpg"
                       @change="onFileSelected($event)"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">JPEG/PNG, max 2 Mo</p>
            </div>

            {{-- Bouton webcam --}}
            <button type="button" @click="ouvrirWebcam()"
                    class="flex items-center gap-2 px-3 py-2 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-medium hover:bg-green-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Prendre une photo
            </button>
        </div>
    </div>

    {{-- Champ caché pour la photo webcam (base64) --}}
    <input type="hidden" name="{{ $name }}_webcam" :value="webcamData" id="webcamData_{{ $name }}">

    {{-- Modal Webcam --}}
    <div x-show="modalOuvert" x-cloak
         class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="fermerWebcam()">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.stop>

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-base">Prendre une photo</h3>
                <button type="button" @click="fermerWebcam()"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-5">

                {{-- Flux webcam --}}
                <div x-show="!photoCapturee" class="relative">
                    <video id="videoStream_{{ $name }}" autoplay playsinline muted
                           class="w-full rounded-xl bg-gray-900"
                           style="aspect-ratio:4/3; object-fit:cover;"></video>

                    {{-- Cadre de visée --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-36 h-44 border-2 border-white/60 rounded-full"></div>
                    </div>

                    <p x-show="erreurCamera" class="absolute inset-0 flex items-center justify-center text-white text-sm bg-gray-900 rounded-xl px-4 text-center" x-text="erreurCamera"></p>
                </div>

                {{-- Photo capturée --}}
                <div x-show="photoCapturee" class="relative">
                    <canvas id="canvas_{{ $name }}"
                            class="w-full rounded-xl border-2 border-green-200"
                            style="aspect-ratio:4/3; object-fit:cover;"></canvas>
                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                        Photo capturee
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex gap-3 mt-4">
                    <template x-if="!photoCapturee">
                        <button type="button" @click="capturer()"
                                :disabled="!cameraActive"
                                class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            </svg>
                            Capturer
                        </button>
                    </template>
                    <template x-if="photoCapturee">
                        <button type="button" @click="reprendrephoto()"
                                class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reprendre
                        </button>
                    </template>
                    <template x-if="photoCapturee">
                        <button type="button" @click="utiliserPhoto()"
                                class="flex-1 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Utiliser cette photo
                        </button>
                    </template>
                    <button type="button" @click="fermerWebcam()"
                            class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function webcamPhoto(fieldName = '') {
    return {
        fieldName: fieldName,
        modalOuvert: false,
        previewUrl: null,
        webcamData: '',
        photoCapturee: false,
        cameraActive: false,
        erreurCamera: '',
        stream: null,

        init() {
            // Arreter le flux webcam si l'utilisateur quitte la page (BUG-020)
            this._beforeUnload = () => this.fermerWebcam();
            window.addEventListener('beforeunload', this._beforeUnload);
        },

        destroy() {
            this.fermerWebcam();
            window.removeEventListener('beforeunload', this._beforeUnload);
        },

        onFileSelected(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => { this.previewUrl = ev.target.result; };
            reader.readAsDataURL(file);
            // Effacer toute donnée webcam précédente
            this.webcamData = '';
        },

        async ouvrirWebcam() {
            this.modalOuvert = true;
            this.photoCapturee = false;
            this.erreurCamera = '';
            this.cameraActive = false;

            await this.$nextTick();

            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }
                });
                const video = document.getElementById('videoStream_' + this.fieldName);
                if (!video) {
                    throw new Error('Video element not found');
                }
                video.srcObject = this.stream;
                this.cameraActive = true;
            } catch (err) {
                if (err.name === 'NotAllowedError') {
                    this.erreurCamera = 'Acces a la camera refuse. Veuillez autoriser la camera dans les parametres du navigateur.';
                } else if (err.name === 'NotFoundError') {
                    this.erreurCamera = 'Aucune camera detectee sur cet appareil.';
                } else {
                    this.erreurCamera = 'Impossible d\'acceder a la camera : ' + err.message;
                }
            }
        },

        capturer() {
            const video  = document.getElementById('videoStream_' + this.fieldName);
            const canvas = document.getElementById('canvas_' + this.fieldName);

            canvas.width  = video.videoWidth  || 640;
            canvas.height = video.videoHeight || 480;

            const ctx = canvas.getContext('2d');
            // Effet miroir (plus naturel pour selfie)
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            this.photoCapturee = true;
        },

        reprendrephoto() {
            this.photoCapturee = false;
        },

        utiliserPhoto() {
            const canvas = document.getElementById('canvas_' + this.fieldName);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

            // Stocker en base64 dans le champ caché
            this.webcamData = dataUrl;
            // Mettre à jour la prévisualisation
            this.previewUrl = dataUrl;

            // Effacer le file input pour éviter les conflits
            const fileInput = document.getElementById('fileInput_' + this.fieldName);
            if (fileInput) {
                fileInput.value = '';
            }

            this.fermerWebcam();
        },

        fermerWebcam() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            this.modalOuvert = false;
            this.cameraActive = false;
            this.photoCapturee = false;
        }
    }
}
</script>
@endpush
@endonce
