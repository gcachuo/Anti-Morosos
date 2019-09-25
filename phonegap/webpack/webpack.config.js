const ExtractTextPlugin = require('extract-text-webpack-plugin');
module.exports = {
    entry: "./",
    output: {
        path: __dirname + "/../www/assets/",
        filename: 'bundle.js'
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.js', ".css", ".scss"],
    },
    module: {
        rules: [
            {test: /\.ts$/, loader: 'ts-loader'},
            {
                test: /\.scss$/,
                loader: ExtractTextPlugin.extract({fallback: 'style-loader', use: 'css-loader!sass-loader'})
            }
        ]
    },
    plugins: [
        new ExtractTextPlugin({filename: 'bundle.css', allChunks: true}),
    ]
};