#!/usr/bin/env python3
# -*- coding: utf-8 -*-

with open('resources/views/auth/auth.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the forgot password link
content = content.replace(
    'href="#">رمز عبور را فراموش کردید؟',
    'href="{{ route(\'password.request\') }}">رمز عبور را فراموش کردید؟'
)

with open('resources/views/auth/auth.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed!")
