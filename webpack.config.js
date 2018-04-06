var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where compiled assets will be stored
    .setOutputPath('public/inc/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/inc')
    .cleanupOutputBeforeBuild()
    //.enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.css)
    //better for cache refresh when new version
    .enableVersioning(Encore.isProduction())

    /**
     * JAVASCRIPTS
     */
    /* ==== JS ==== */
    // global common js
    .addEntry('js/main','./assets/main.js')
    // product when connected
    .addEntry('js/product', './assets/product.js')
    /* ==== JS LIB ==== */
    // JS for scanner
    .addEntry('js/scan','./assets/scan.js')
    // JS for dropify
    .addEntry('js/dropify','./assets/dropify.js')
    /**
     * STYLESHEETS
     */
    // CSS MAIN
    .addStyleEntry('css/main','./assets/main.scss')
    // CSS SCAN
    .addStyleEntry('css/scan','./assets/scan.scss')
    // CSS DROPIFY
    .addStyleEntry('css/dropify', './assets/dropify.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
