# Ideas


### Smart thumbnails

Using select and fps filters for making more meaningful thumbnails.

```sh
$ ffmpeg -ss 1 -i ./new_intro.webm -vf "select=gt(scene\,0.5),nlmeans,scale=640:360" -frames:v 5 -vsync vfr -vf fps=1/60 out%02d.jpg
```
