## Video services

> Video related services requires ffmpeg and/or ffprobe installed. 

- [x] **VideoConverter** ([doc here](./video-conversion-service.md))
      - [x] Conversions: transcode, compress, transmux...
      - [x] Clipping (start-time/send-time)
      - [x] Filters: scale, deinterlace, denoise.        
        ```php hl_lines="4 5 6 7 8 9 10 12 13 14 15 16"
        <?php // A quick taste            
        $converter = new VideoConverter(new FFMpegConfig('/path/to/ffmpeg'));
        
        $params = (new VideoConvertParams())
            ->withVideoCodec('libx264')    
            ->withStreamable(true)
            ->withVideoFilter(
                new ScaleFilter(720, 576)
            )
            ->withCrf(24);                  
            
        $converter->convert(
            '/path/inputFile.mov', 
            '/path/outputFile.mp4', 
            $params
        );           
        ```  
      
- [x] **VideoInfoReader** ([doc here](./video-info-service.md))
      - [x] Duration, dimensions, number of frames
        ```php hl_lines="5 7 8 9 10"
        <?php // a quick taste
        
        $infoReader = new VideoInfoReader(new FFProbeConfig('/path/to/ffprobe'));
        
        $videoInfo = $infoReader->getInfo('/path/video.mp4');
        
        $duration = $videoInfo->getDuration();
        $frames   = $videoInfo->getNbFrames();
        $width    = $videoInfo->getWidth();
        $height   = $videoInfo->getHeight();
        ```  

- [x] **VideoThumbGenerator** ([doc here](./video-thumb-service.md))
      - [x] Thumbnail at specific time
      - [x] Support filters: scale, denoise, deinterlace.
        ```php hl_lines="4 5 6 7 8 9 11 12 13 14 15"
        <?php // a quick taste        
        $generator = new VideoThumbGenerator(new FFMpegConfig('/path/to/ffmpeg'));
        
        $params = (new VideoThumbParams())
            ->withVideoFilter(
                new ScaleFilter(720, 576)
            )        
            ->withTime(1.25);
            
        $generator->makeThumbnail(
            '/path/inputFile.mov', 
            '/path/outputFile.jpg', 
            $params
        );    
        ```  

- [x] **VideoAnalyzer** ([doc here](./video-detection-service.md))
      - [x] Interlacing detection
        ```php hl_lines="4 6 7 8 9 10"
        <?php // a quick taste        
        $analyzer = new VideoAnalyzer(new FFMpegConfig('/path/to/ffmpeg'));
        
        $interlaceGuess = $analyzer->detectInterlacement();
                    
        $interlaced = $interlaceGuess->isInterlaced();
        ```  
     

