import sys

file_path = r'c:\xampp\htdocs\AN_Bakery\index.php'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
in_style = False
in_script = False

for i, line in enumerate(lines):
    if line.strip() == '<style>' and i+1 < len(lines) and 'Bakery Theme' in lines[i+1]:
        in_style = True
        new_lines.append('<link rel=\"stylesheet\" href=\"<?= url(\'assets/css/index.css\') ?>?v=<?= time() ?>\">\\n')
        continue
    if in_style and line.strip() == '</style>':
        in_style = False
        continue
    if in_style:
        continue

    if line.strip() == '<script>' and i+1 < len(lines) and 'let isSliding' in lines[i+1]:
        in_script = True
        new_lines.append('<script src=\"<?= url(\'assets/js/index.js\') ?>?v=<?= time() ?>\"></script>\\n')
        continue
    if in_script and line.strip() == '</script>':
        in_script = False
        continue
    if in_script:
        continue

    new_lines.append(line)

content = ''.join(new_lines)
if \"?>clude 'includes/layouts/footer.php';\" in content:
    content = content.replace(\"?>clude 'includes/layouts/footer.php';\\n?>\", \"?>\")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
