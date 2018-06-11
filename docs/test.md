# Test

```
#!/bin/sh

#
# For the mp4 version
#

# 1.1. Ignore interlacing, no denoising

/opt/ffmpeg/ffmpeg -i '/web/material-for-the-spine/sources/videos_mov/hello.mov' -vcodec h264 -acodec aac -pix_fmt yuv420p -movflags +faststart -threads 7 '/web/material-for-the-spine/converted/videos/hello_nodeint_nodenoise.mp4'

# -> 15.6 Mb

# 1.2. Deinnterlace, no denoising

/opt/ffmpeg/ffmpeg -i '/web/material-for-the-spine/sources/videos_mov/hello.mov' -vcodec h264 -acodec aac -pix_fmt yuv420p -movflags +faststart -threads 7 '/web/material-for-the-spine/converted/videos/hello_deint_nodenoise.mp4'

# 1.3. Deinterlace, denoise

/opt/ffmpeg/ffmpeg -i '/web/material-for-the-spine/sources/videos_mov/hello.mov' -vcodec h264 -acodec aac -pix_fmt yuv420p -movflags +faststart -threads 7 '/web/material-for-the-spine/converted/videos/hello_deint_denoise.mp4'

#
# 2. For the webm version
#

# 2.1. Ignore interlacing, no denoising

/opt/ffmpeg/ffmpeg -i '/web/material-for-the-spine/sources/videos_mov/hello.mov' -vcodec libvpx-vp9 -b:v 750k -quality good -crf 33 -acodec libopus -b:a 128k -g 240 -tile-columns 2 -frame-parallel 1 -speed 0 -pix_fmt yuv420p -f webm -threads 7 '/web/material-for-the-spine/hello_nodeint_nodenoise.webm'

# -> 18.4Mb (Not bad)

# 2.2. Deinterlace, no denoising

/opt/ffmpeg/ffmpeg -i '/web/material-for-the-spine/sources/videos_mov/hello.mov' -vcodec libvpx-vp9 -b:v 750k -vf yadif=mode=0:parity=1:deint=0 -quality good -crf 33 -acodec libopus -b:a 128k -g 240 -tile-columns 2 -frame-parallel 1 -speed 0 -pix_fmt yuv420p -f webm -threads 7 '/web/material-for-the-spine/hello_deint_nodenoise.webm'

# -> 15.8Mb (Not bad)

# 2.3. Deinterlace, denoise

/opt/ffmpeg/ffmpeg -i '/web/material-for-the-spine/sources/videos_mov/hello.mov' -vcodec libvpx-vp9 -b:v 750k -vf yadif=mode=0:parity=1:deint=0,hqdn3d -quality good -crf 33 -acodec libopus -b:a 128k -g 240 -tile-columns 2 -frame-parallel 1 -speed 0 -pix_fmt yuv420p -f webm -threads 7 '/web/material-for-the-spine/hello_deint_denoise.webm'

# -> 14.2Mb (Good size, a bit blurred)

```
