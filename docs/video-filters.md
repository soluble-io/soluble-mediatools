

## Video filters

Both `VideoConverter` and `VideoThumbGenerator` services allows setting up video filters through
the `->withVideoFilter(VideoFilterInterface $videoFilter)` method. 

???+ question "What are video filters used for ? "
    We try to keep close to what ffmpeg does. In ffmpeg 
    filters accomplish tasks as different as cropping, scaling, 
    identifying (interlace, black detection), denoising, parts selection, 
    colouring, generators...
    
    The complete list of ffmpeg filters can be found [here](https://ffmpeg.org/ffmpeg-filters.html)
 
 
Mediatools provide some common filters implementations but it's very easy to create your own, see last section.

```php
<?php
use Soluble\MediaTools\Video\Filter;

$params = (new VideoConvertParams())
    ->withVideoFilter(
        new Filter\VideoFilterChain([
            // A scaling filter
            new Filter\ScaleFilter(800, 600),
            // A denoise filter
            new Filter\Hqdn3DVideoFilter(),
            // A custom filter
            new class implements Filter\Type\FFMpegVideoFilterInterface {
                public function getFFmpegCLIValue(): string {
                    return 'frei0r=vertigo:0.2';
                }
            }
        ])
    );

```
   
### Built-in filters    


| Filter                   | Type          | Argument(s)                            | Link(s)    | 
| ------------------------ | ------------- | -------------------------------------- | ---------- |
| `ScaleFilter`            | Dimension     | $width, $height, ?$aspect_ratio_mode...   | [src](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/ScaleFilter.php) |
| `CropFilter`             |               | ?$width, ?$height, ?$x, ?              | [src](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/CropFilter.php) |            
| `YadifVideoFilter`       | Deinterlace   | ?$mode, ?$parity, ?$deint              | [src](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/YadifVideoFilter.php) |            
| `Hqdn3DVideoFilter`      | Denoise       |                                        | [src](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/Hqdn3DVideoFilter.php) |           
| `NlmeansVideoFilter`     | Denoise (slow)|                                        | [src](https://github.com/soluble-io/soluble-mediatools/blob/master/src/Video/Filter/NlmeansVideoFilter.php) |         


### Chaining filters

#### VideoFilterChain

To apply multiple filters, use the `VideoFilterChain` object. Filters will be processed
in the order they've been added.

```php
<?php
use Soluble\MediaTools\Video\Filter;

// from the constructor
$filters = new Filter\VideoFilterChain([    
    new Filter\YadifVideoFilter(),
    new Filter\Hqdn3DVideoFilter() 
]);

// Alternatively, use ->addFilter method
$filters->addFilter(new Filter\NlmeansVideoFilter());

$params = (new VideoConvertParams())
    ->withVideoFilter($filters);

// ....

```

???+ question "Filter graph support ?"
     FFMpeg support a complex notation for advanced filter chaining named
     [filtergraph](http://ffmpeg.org/ffmpeg-filters.html#Filtergraph-description).
     
     The `VideoFilterChain` does not support this notation to keep the surface API
     as intuitive as possible. If you need to use the filtergraph notation, you'll 
     need to create your own filter:   
     
     ```php
     <?php
     $myComplexFilter = new class interface FFMpegVideoFilterInterface {
        public function getFFmpegCLIValue(): string
        {
             return '[in]yadif=0:0:0[middle];[middle]scale=iw/2:-1[out]';
        }
     }
     ```
   
     We :heart: contributions, if you have nice ideas about how to support filtergraph, 
     make your voice loud in this [issue](https://github.com/soluble-io/soluble-mediatools/issues/10)
    

### Custom filter

Making your own filter is easy, you just have to implement `FFMpegVideoFilterInterface`:

```php
<?php
use Soluble\MediaTools\Video\Filter\Type\FFMpegVideoFilterInterface;

$vertigoFilter = new class implements FFMpegVideoFilterInterface {
    public function getFFmpegCLIValue(): string
    {
        return 'frei0r=vertigo:0.2';
    }
};

```

And voilà !







