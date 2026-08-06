#!/usr/bin/env python3
from pathlib import Path
import re
root=Path(__file__).resolve().parents[1]
plugin=root/'sabri-welcome-intro'
patterns=[
 re.compile(r"(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'((?:\\'|[^'])*)'\s*,\s*'sabri-welcome-intro'"),
 re.compile(r'(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*"((?:\\"|[^"])*)"\s*,\s*"sabri-welcome-intro"'),
]
messages={}
for p in sorted(plugin.rglob('*.php')):
    text=p.read_text(encoding='utf-8')
    for pat in patterns:
        for m in pat.finditer(text):
            msg=m.group(1).replace("\\'", "'").replace('\\"','"')
            line=text.count('\n',0,m.start())+1
            messages.setdefault(msg,[]).append(f'{p.relative_to(plugin).as_posix()}:{line}')
def q(s): return '"'+s.replace('\\','\\\\').replace('"','\\"').replace('\n','\\n')+'"'
out=['msgid ""','msgstr ""','"Project-Id-Version: Sabri Welcome Intro Animation 1.0.0\\n"','"POT-Creation-Date: 2026-08-06 13:14+0000\\n"','"MIME-Version: 1.0\\n"','"Content-Type: text/plain; charset=UTF-8\\n"','"Content-Transfer-Encoding: 8bit\\n"','']
for msg in sorted(messages):
    out.append('#: '+' '.join(messages[msg]))
    out.append('msgid '+q(msg))
    out.append('msgstr ""')
    out.append('')
(plugin/'languages/sabri-welcome-intro.pot').write_text('\n'.join(out),encoding='utf-8')
print(f'POT: {len(messages)} messages')
