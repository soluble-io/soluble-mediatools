# Installation


## Composer

Be sure to have php7.1 installed and add soluble/mediatools to your project dependencies.

```bash
$ composer require soluble/mediatools
```

## FFMpeg

### Installation

=== "Ubuntu/Linux"

    ```bash
    $ sudo apt install ffmpeg
    ```

    > Or choose a static build by downloading latest [https://johnvansickle.com/ffmpeg/](https://johnvansickle.com/ffmpeg/)


=== "Travis/CI"

     As an example look to the [travis install script](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh).

### Notes

> This library relies on FFMpeg binaries
>
> - **[ffmpeg](https://ffmpeg.org/ffmpeg.html)** is required by `VideoConverter`, `VideoVideoAnalyzer` and `VideoThumbGenerator`.
> - **[ffprobe](https://ffmpeg.org/ffprobe.html)** is required by `VideoInfoReader`.
>



