# PDF Thumbnail Generator

This PHP app creates image thumbnails from PDF file names passed to it.

## System Requirements

- PHP 8.2
- ImageMagick with ghostscript


## Example configuration

In the `example` directory you can see a docker-compose setup that uses
the thumbnail generator as a proxy for missing images.

On the first run, the `thumbnails` directory is empty. On reload, the web
server will serve the generated files.

## Future Improvements
At the moment, this is a simple script and thin wrapper around a
shell call to `convert`. In the future, I can imagine the following
features:

- Give image dimensions and output type (png, webm) via file name
- More flexible name lookup
- Use PHP integration of ImageMagick
- Add support for more input file types
- Command line tool
