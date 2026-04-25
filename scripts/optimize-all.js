import sharp from 'sharp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const publicImagesDir = path.join(__dirname, '../public/assets/images');

async function processDirectory(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);

        if (stat.isDirectory()) {
            await processDirectory(fullPath);
        } else if (stat.isFile() && /\.(png|jpe?g)$/i.test(file)) {
            // Ignore already optimized files or specific files
            if (file.includes('-opt') || file.endsWith('.webp')) continue;

            const ext = path.extname(file);
            const baseName = path.basename(file, ext);
            
            // For branding images (like doctors), resize more aggressively
            const isBranding = fullPath.includes('branding');
            const targetWidth = isBranding ? 800 : 600; // max width

            const newFileName = `${baseName}.webp`;
            const newFilePath = path.join(dir, newFileName);

            try {
                const metadata = await sharp(fullPath).metadata();
                
                let transform = sharp(fullPath);
                
                // Only resize if the image is larger than targetWidth
                if (metadata.width > targetWidth) {
                    transform = transform.resize(targetWidth, null, { withoutEnlargement: true });
                }

                await transform
                    .webp({ quality: 80, effort: 6 })
                    .toFile(newFilePath);

                const oldSize = fs.statSync(fullPath).size;
                const newSize = fs.statSync(newFilePath).size;
                const savings = ((oldSize - newSize) / oldSize * 100).toFixed(1);

                console.log(`[CONVERTED] ${file} -> ${newFileName} | Savings: ${savings}% (${Math.round(oldSize/1024)}KB -> ${Math.round(newSize/1024)}KB)`);

                fs.unlinkSync(fullPath);

            } catch (err) {
                console.error(`[ERROR] Failed to process ${file}:`, err.message);
            }
        }
    }
}

async function run() {
    console.log('Starting massive image optimization...');
    await processDirectory(publicImagesDir);
    console.log('Optimization complete!');
}

run();
