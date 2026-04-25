const sharp = require('sharp');
const path = require('path');

const base = path.join(__dirname, '../public/assets/images/branding');

async function compress() {
    // Compress dokter1.png (4.3MB → target ~200KB)
    await sharp(`${base}/dokter1.png`)
        .resize(600, null, { withoutEnlargement: true })
        .png({ quality: 75, compressionLevel: 9 })
        .toFile(`${base}/dokter1-opt.png`);
    
    const s1 = require('fs').statSync(`${base}/dokter1-opt.png`);
    console.log(`dokter1-opt.png: ${Math.round(s1.size/1024)}KB`);

    // Compress dokter.png (785KB → target ~100KB)
    await sharp(`${base}/dokter.png`)
        .resize(500, null, { withoutEnlargement: true })
        .png({ quality: 75, compressionLevel: 9 })
        .toFile(`${base}/dokter-opt.png`);
    
    const s2 = require('fs').statSync(`${base}/dokter-opt.png`);
    console.log(`dokter-opt.png: ${Math.round(s2.size/1024)}KB`);

    // Compress logo
    await sharp(`${base}/pharmacare-logo.png`)
        .resize(200, null, { withoutEnlargement: true })
        .png({ quality: 80, compressionLevel: 9 })
        .toFile(`${base}/pharmacare-logo-opt.png`);
    
    const s3 = require('fs').statSync(`${base}/pharmacare-logo-opt.png`);
    console.log(`pharmacare-logo-opt.png: ${Math.round(s3.size/1024)}KB`);

    console.log('Done!');
}

compress().catch(console.error);
