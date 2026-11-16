import os

root_dirs = [r"D:\laragon\www\UI", r"D:\laragon\www\Laundrey", r"D:\laragon\www\LaundreyGo", r"D:\laragon\www\laundrygo"]
target = "CUST-001"

for rdir in root_dirs:
    for root, dirs, files in os.walk(rdir):
        for f in files:
            if f.endswith(('.php', '.html', '.js')):
                fpath = os.path.join(root, f)
                try:
                    with open(fpath, 'r', encoding='utf-8', errors='ignore') as fp:
                        content = fp.read()
                        if target in content:
                            print(f"FOUND {target} in: {fpath}")
                except Exception as e:
                    pass
