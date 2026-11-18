new_code = '''/* ========================================================================
   TOAST NOTIFICATION (Unified Mobile Shell createui Toast)
   ======================================================================== */
function showToast(msg, withUndo = false) {
  if (typeof window.showToast === 'function') {
    const action = withUndo ? {
      text: 'Urungkan',
      onClick: function() {
        if (typeof rollbackLastAction === 'function') rollbackLastAction();
      }
    } : null;
    window.showToast(msg, 'info', 'Lantai Produksi', 4500, action);
    return;
  }
}'''

file_path = r'd:\laragon\www\Laundrey\app\Views\produksi\index.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

start_marker = '/* ========================================================================\n   TOAST NOTIFICATION TRAY'
if start_marker not in content:
    start_marker = '/* ========================================================================\r\n   TOAST NOTIFICATION TRAY'

end_marker = '// Start on DOM ready'

start_pos = content.find('TOAST NOTIFICATION TRAY')
# Find preceding /*
start_pos = content.rfind('/*', 0, start_pos)
end_pos = content.find(end_marker)

if start_pos == -1 or end_pos == -1:
    print('ERROR: markers not found!', start_pos, end_pos)
    exit(1)

is_crlf = '\r\n' in content
replacement = new_code.replace('\r\n', '\n')
if is_crlf:
    replacement = replacement.replace('\n', '\r\n') + '\r\n\r\n'
else:
    replacement = replacement + '\n\n'

new_content = content[:start_pos] + replacement + content[end_pos:]

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print('SUCCESSFULLY UPDATED produksi/index.php!')
