import Alpine from 'alpinejs';

// Vosk — Speech-to-Text (local, no CDN)
import * as Vosk from 'vosk-browser';
window.Vosk = Vosk;

window.Alpine = Alpine;
Alpine.start();
