# PDF Thumbnail Generator

This PHP app creates image thumbnails from PDF file names passed to it.

## Configuration

The script needs two configuration variables to work:

- `SOURCE_PATH` - The directory where the script can find the PDFs.
- `THUMBNAIL_PATH` - The directory where the script will put the generated
	thumbnails. The directory must be writable.

If you host the app in a subdirectory you can strip the subdirectory path
with the variable `URL_PREFIX`.

Example: *`SOURCE_PATH` is `/var/www/pdf`, `THUMBNAIL_PATH` is
`/var/www/images`, the web server document root is `/var/www/htdocs`,
`URL_PREFIX` is `/thumbnail`. The user requests the URL
`https://example.com/thumbnail/2020/03/info.pdf.jpg`. The script will
strip the host name, `/thumbnail` prefix and `.jpg` suffix from the URL
and look for the file `/var/www/pdf/2020/03/info.pdf` and will create
the file `/var/www/images/2020/03/info.pdf.jpg`*


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
