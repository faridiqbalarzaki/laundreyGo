import os

views_dir = r"d:\laragon\www\Laundrey\app\Views"
found = []
for root, dirs, files in os.walk(views_dir):
    for f in files:
        if f.endswith('.php'):
            p = os.path.join(root, f)
            with open(p, 'r', encoding='utf-8', errors='ignore') as fl:
                content = fl.read()
                if '<style' in content:
                    found.append(p)

print("Views with <style>:", len(found))
for item in found:
    print(" - ", item)
