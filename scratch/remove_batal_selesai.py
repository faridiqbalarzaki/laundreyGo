import re

p = r'd:\laragon\www\Laundrey\app\Views\pesanan\selesai.php'
with open(p, 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
skip = False

for line in lines:
    if '<form action="<?= \\App\\Core\\Helper::url(\'pesanan\', [\'action\' => \'updateStatus\']) ?>"' in line:
        skip = True
        continue
    if skip:
        if '</form>' in line:
            skip = False
        continue
    new_lines.append(line)

with open(p, 'w', encoding='utf-8') as f:
    f.writelines(new_lines)

print("SUCCESS: Batal Serahkan form removed from selesai.php")
