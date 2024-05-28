# glpi-pdf
print_pdf hook for Suppliers GLPI

- Add file supplier.class.php in directory /var/www/html/glpi/plugins/pdf/inc/
- Add hook line 82 in /var/www/html/glpi/plugins/pdf/setup.php : $PLUGIN_HOOKS['plugin_pdf']['Supplier']           = 'PluginPdfSupplier';
- Restart the plugin
