var Encore = require('@symfony/webpack-encore');

Encore
// directory where compiled assets will be stored
    .setOutputPath('public/assets/')
    // public path used by the web server to access the output path
    .setPublicPath('/assets')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    // .addEntry('app', './assets/js/app.js')
    .addStyleEntry('css/dashboard', ['./assets/css/dashboard.css'])
    .addStyleEntry('css/bootstrap', ['./assets/css/bootstrap.css'])

    // .addEntry('js/jquery-3.3.1', './assets/js/jquery-3.3.1.js')
    // .addEntry('js/popper', './assets/js/popper.js')
    .addEntry('js/bootstrap', './assets/js/bootstrap.js')
    .addEntry('js/feather.min', './assets/js/feather.min.js')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    // .enableSingleRuntimeChunk()

    // .cleanupOutputBeforeBuild()
    // .enableSourceMaps(!Encore.isProduction())
// enables hashed filenames (e.g. app.abc123.css)
// .enableVersioning(Encore.isProduction())

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment if you use Sass/SCSS files
//.enableSassLoader()

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()
;

var Encore = require('@symfony/webpack-encore');

module.exports = Encore.getWebpackConfig();