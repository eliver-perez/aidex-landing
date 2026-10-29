// Formatea el HTML conservando intactos los bloques PHP del servidor.
const fs = require('node:fs');
const path = require('node:path');
const prettier = require('prettier');

(async () => {
    const file = path.join(__dirname, '../public/index.php');
    const blocks = [];
    const source = fs.readFileSync(file, 'utf8').replace(/<\?(?:php|=)[\s\S]*?\?>/g, (block) => {
        const index = blocks.push(block) - 1;
        return block.startsWith('<?=') ? `PHPECHO${index}END` : `<!-- PHPBLOCK${index}END -->`;
    });
    const formatted = await prettier.format(source, {
        parser: 'html',
        tabWidth: 4,
        printWidth: 100,
        htmlWhitespaceSensitivity: 'ignore'
    });
    const restored = formatted.replace(
        /<!-- PHPBLOCK(\d+)END -->|PHPECHO(\d+)END/g,
        (_, block, echo) => {
            return blocks[Number(block ?? echo)];
        }
    );
    fs.writeFileSync(file, restored);
})().catch((error) => {
    console.error(error.message);
    process.exitCode = 1;
});
