import os
import re

directory = r"c:\xampp\htdocs\AN_Bakery"

colors = set()
font_families = set()
font_sizes = set()
icons = set()
logos = set()

color_re = re.compile(r'#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b|rgba?\([^)]+\)|hsla?\([^)]+\)', re.IGNORECASE)
ff_re = re.compile(r'font-family:\s*([^;}]+)')
fs_re = re.compile(r'font-size:\s*([^;}]+)')
icon_re = re.compile(r'class=["\']([^"\']*\b(?:fa|fas|fab|far|fal|bx|bxl|bx-)\b[^"\']*)["\']')
logo_re = re.compile(r'src=["\']([^"\']*(?:logo)[^"\']*)["\']', re.IGNORECASE)

for root, dirs, files in os.walk(directory):
    if ".git" in root: continue
    for filename in files:
        filepath = os.path.join(root, filename)
        if filename.endswith(".css"):
            try:
                with open(filepath, "r", encoding="utf-8") as f:
                    content = f.read()
                    for match in color_re.finditer(content):
                        colors.add(match.group(0).lower().strip())
                    for match in ff_re.finditer(content):
                        font_families.add(match.group(1).strip().strip("'\""))
                    for match in fs_re.finditer(content):
                        font_sizes.add(match.group(1).strip())
            except Exception:
                pass
        if filename.endswith(".php") or filename.endswith(".html"):
            try:
                with open(filepath, "r", encoding="utf-8") as f:
                    content = f.read()
                    for match in icon_re.finditer(content):
                        classes = match.group(1).split()
                        for cls in classes:
                            if cls.startswith('fa-') or cls.startswith('bx-'):
                                icons.add(cls)
                    for match in logo_re.finditer(content):
                        logos.add(match.group(1))
            except Exception:
                pass

print("=== COLORS ===")
for c in sorted(list(colors)): print(c)
print("\n=== FONT FAMILIES ===")
for c in sorted(list(font_families)): print(c)
print("\n=== FONT SIZES ===")
for c in sorted(list(font_sizes)): print(c)
print("\n=== ICONS ===")
for c in sorted(list(icons)): print(c)
print("\n=== LOGOS ===")
for c in sorted(list(logos)): print(c)
