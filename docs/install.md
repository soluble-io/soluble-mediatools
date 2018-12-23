# Installation


## Composer

Be sure to have php7.1 installed and add soluble/mediatools to your project dependencies.

```bash
$ composer require soluble/mediatools
``` 

## FFMpeg

> This library relies on FFMpeg binaries 
>
> - **[ffmpeg](https://ffmpeg.org/ffmpeg.html)** is required by `VideoConverter`, `VideoVideoAnalyzer` and `VideoThumbGenerator`.
> - **[ffprobe](https://ffmpeg.org/ffprobe.html)** is required by `VideoInfoReader`.
>  
> Statically [compiled binaries](https://ffmpeg.org/download.html) exists in case your OS does not provide them.
>
> *Tip: For integration tests see our [ffmpeg travis install](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh) script*. 
 
### For Linux

> Most distributions offers ffmpeg in their repositories. If you're not happy with
> their version, use a static build instead.

#### Distribution based

Ubuntu flavors:

```bash
$ sudo apt install ffmpeg
```

#### Static builds

For linux, you can easily download ffmpeg/ffprobe statically compiled binaries at

- [https://johnvansickle.com/ffmpeg/](https://johnvansickle.com/ffmpeg/)

#### Travis/CI notes 

As an example look to the [travis install script](https://github.com/soluble-io/soluble-mediatools/blob/master/.travis/travis-install-ffmpeg.sh).
