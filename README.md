# Census Block Lookup Plugin

This plugin uses the FCC API (located at https://geo.fcc.gov/api/census/#!/block/get_block_find) to get the Census Block ID (or FIPS) from a Longitude and Latitude value.  This is useful because the UCRM only automatically fills this in if an address can be geocoded.

This plugin can be accessed from the main menu under "Census Block Lookup".

This plugin is based on the "Revenue Report" Plugin.

## Useful classes

### `App\Service\TemplateRenderer`

Very simple class to load a PHP template. When writing a PHP template be careful to use correct escaping function: `echo htmlspecialchars($string, ENT_QUOTES);`.

### UCRM Plugin SDK
The [UCRM Plugin SDK](https://github.com/Ubiquiti-App/UCRM-Plugin-SDK) is used by this plugin. It contains classes able to help you with calling UCRM API, getting plugin's configuration and much more.

