<?php

declare(strict_types=1);

/**
 * @see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
 *
 * @copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
 * @license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
 */

namespace MediaToolsTest\Util;

interface CodecInterface
{
    /**
     * Uncompressed 4:2:2 10-bit.
     */
    public const VIDEO_012V = '012v';
    /**
     * 4X Movie.
     */
    public const VIDEO_4XM = '4xm';
    /**
     * QuickTime 8BPS video.
     */
    public const VIDEO_8BPS = '8bps';
    /**
     * Multicolor charset for Commodore 64 (encoders: a64multi ).
     */
    public const VIDEO_A64_MULTI = 'a64_multi';
    /**
     * Multicolor charset for Commodore 64, extended with 5th color (colram) (encoders: a64multi5 ).
     */
    public const VIDEO_A64_MULTI5 = 'a64_multi5';
    /**
     * Autodesk RLE.
     */
    public const VIDEO_AASC = 'aasc';
    /**
     * Apple Intermediate Codec.
     */
    public const VIDEO_AIC = 'aic';
    /**
     * Alias/Wavefront PIX image.
     */
    public const VIDEO_ALIAS_PIX = 'alias_pix';
    /**
     * AMV Video.
     */
    public const VIDEO_AMV = 'amv';
    /**
     * Deluxe Paint Animation.
     */
    public const VIDEO_ANM = 'anm';
    /**
     * ASCII/ANSI art.
     */
    public const VIDEO_ANSI = 'ansi';
    /**
     * APNG (Animated Portable Network Graphics) image.
     */
    public const VIDEO_APNG = 'apng';
    /**
     * ASUS V1.
     */
    public const VIDEO_ASV1 = 'asv1';
    /**
     * ASUS V2.
     */
    public const VIDEO_ASV2 = 'asv2';
    /**
     * Auravision AURA.
     */
    public const VIDEO_AURA = 'aura';
    /**
     * Auravision Aura 2.
     */
    public const VIDEO_AURA2 = 'aura2';
    /**
     * Alliance for Open Media AV1 (decoders: libaom-av1 ) (encoders: libaom-av1 ).
     */
    public const VIDEO_AV1 = 'av1';
    /**
     * Avid AVI Codec.
     */
    public const VIDEO_AVRN = 'avrn';
    /**
     * Avid 1:1 10-bit RGB Packer.
     */
    public const VIDEO_AVRP = 'avrp';
    /**
     * AVS (Audio Video Standard) video.
     */
    public const VIDEO_AVS = 'avs';
    /**
     * AVS2-P2/IEEE1857.4.
     */
    public const VIDEO_AVS2 = 'avs2';
    /**
     * Avid Meridien Uncompressed.
     */
    public const VIDEO_AVUI = 'avui';
    /**
     * Uncompressed packed MS 4:4:4:4.
     */
    public const VIDEO_AYUV = 'ayuv';
    /**
     * Bethesda VID video.
     */
    public const VIDEO_BETHSOFTVID = 'bethsoftvid';
    /**
     * Brute Force & Ignorance.
     */
    public const VIDEO_BFI = 'bfi';
    /**
     * Bink video.
     */
    public const VIDEO_BINKVIDEO = 'binkvideo';
    /**
     * Binary text.
     */
    public const VIDEO_BINTEXT = 'bintext';
    /**
     * Bitpacked.
     */
    public const VIDEO_BITPACKED = 'bitpacked';
    /**
     * BMP (Windows and OS/2 bitmap).
     */
    public const VIDEO_BMP = 'bmp';
    /**
     * Discworld II BMV video.
     */
    public const VIDEO_BMV_VIDEO = 'bmv_video';
    /**
     * BRender PIX image.
     */
    public const VIDEO_BRENDER_PIX = 'brender_pix';
    /**
     * Interplay C93.
     */
    public const VIDEO_C93 = 'c93';
    /**
     * Chinese AVS (Audio Video Standard) (AVS1-P2, JiZhun profile).
     */
    public const VIDEO_CAVS = 'cavs';
    /**
     * CD Graphics video.
     */
    public const VIDEO_CDGRAPHICS = 'cdgraphics';
    /**
     * Commodore CDXL video.
     */
    public const VIDEO_CDXL = 'cdxl';
    /**
     * Cineform HD.
     */
    public const VIDEO_CFHD = 'cfhd';
    /**
     * Cinepak.
     */
    public const VIDEO_CINEPAK = 'cinepak';
    /**
     * Iterated Systems ClearVideo.
     */
    public const VIDEO_CLEARVIDEO = 'clearvideo';
    /**
     * Cirrus Logic AccuPak.
     */
    public const VIDEO_CLJR = 'cljr';
    /**
     * Canopus Lossless Codec.
     */
    public const VIDEO_CLLC = 'cllc';
    /**
     * Electronic Arts CMV video (decoders: eacmv ).
     */
    public const VIDEO_CMV = 'cmv';
    /**
     * CPiA video format.
     */
    public const VIDEO_CPIA = 'cpia';
    /**
     * CamStudio (decoders: camstudio ).
     */
    public const VIDEO_CSCD = 'cscd';
    /**
     * Creative YUV (CYUV).
     */
    public const VIDEO_CYUV = 'cyuv';
    /**
     * Daala.
     */
    public const VIDEO_DAALA = 'daala';
    /**
     * DirectDraw Surface image decoder.
     */
    public const VIDEO_DDS = 'dds';
    /**
     * Chronomaster DFA.
     */
    public const VIDEO_DFA = 'dfa';
    /**
     * Dirac (encoders: vc2 ).
     */
    public const VIDEO_DIRAC = 'dirac';
    /**
     * VC3/DNxHD.
     */
    public const VIDEO_DNXHD = 'dnxhd';
    /**
     * DPX (Digital Picture Exchange) image.
     */
    public const VIDEO_DPX = 'dpx';
    /**
     * Delphine Software International CIN video.
     */
    public const VIDEO_DSICINVIDEO = 'dsicinvideo';
    /**
     * DV (Digital Video).
     */
    public const VIDEO_DVVIDEO = 'dvvideo';
    /**
     * Feeble Files/ScummVM DXA.
     */
    public const VIDEO_DXA = 'dxa';
    /**
     * Dxtory.
     */
    public const VIDEO_DXTORY = 'dxtory';
    /**
     * Resolume DXV.
     */
    public const VIDEO_DXV = 'dxv';
    /**
     * Escape 124.
     */
    public const VIDEO_ESCAPE124 = 'escape124';
    /**
     * Escape 130.
     */
    public const VIDEO_ESCAPE130 = 'escape130';
    /**
     * OpenEXR image.
     */
    public const VIDEO_EXR = 'exr';
    /**
     * FFmpeg video codec #1.
     */
    public const VIDEO_FFV1 = 'ffv1';
    /**
     * Huffyuv FFmpeg variant.
     */
    public const VIDEO_FFVHUFF = 'ffvhuff';
    /**
     * Mirillis FIC.
     */
    public const VIDEO_FIC = 'fic';
    /**
     * FITS (Flexible Image Transport System).
     */
    public const VIDEO_FITS = 'fits';
    /**
     * Flash Screen Video v1.
     */
    public const VIDEO_FLASHSV = 'flashsv';
    /**
     * Flash Screen Video v2.
     */
    public const VIDEO_FLASHSV2 = 'flashsv2';
    /**
     * Autodesk Animator Flic video.
     */
    public const VIDEO_FLIC = 'flic';
    /**
     * FLV / Sorenson Spark / Sorenson H.263 (Flash Video) (decoders: flv ) (encoders: flv ).
     */
    public const VIDEO_FLV1 = 'flv1';
    /**
     * FM Screen Capture Codec.
     */
    public const VIDEO_FMVC = 'fmvc';
    /**
     * Fraps.
     */
    public const VIDEO_FRAPS = 'fraps';
    /**
     * Forward Uncompressed.
     */
    public const VIDEO_FRWU = 'frwu';
    /**
     * Go2Meeting.
     */
    public const VIDEO_G2M = 'g2m';
    /**
     * Gremlin Digital Video.
     */
    public const VIDEO_GDV = 'gdv';
    /**
     * GIF (Graphics Interchange Format).
     */
    public const VIDEO_GIF = 'gif';
    /**
     * H.261.
     */
    public const VIDEO_H261 = 'h261';
    /**
     * H.263 / H.263-1996, H.263+ / H.263-1998 / H.263 version 2 (decoders: h263 h263_v4l2m2m ) (encoders: h263 h263_v.
     */
    public const VIDEO_H263 = 'h263';
    /**
     * Intel H.263.
     */
    public const VIDEO_H263I = 'h263i';
    /**
     * H.263+ / H.263-1998 / H.263 version 2.
     */
    public const VIDEO_H263P = 'h263p';
    /**
     * H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10 (decoders: h264 h264_v4l2m2m ) (encoders: libx264 libx264rgb h264_v4l.
     */
    public const VIDEO_H264 = 'h264';
    /**
     * Vidvox Hap.
     */
    public const VIDEO_HAP = 'hap';
    /**
     * H.265 / HEVC (High Efficiency Video Coding) (encoders: libx265 hevc_vaapi ).
     */
    public const VIDEO_HEVC = 'hevc';
    /**
     * HNM 4 video.
     */
    public const VIDEO_HNM4VIDEO = 'hnm4video';
    /**
     * Canopus HQ/HQA.
     */
    public const VIDEO_HQ_HQA = 'hq_hqa';
    /**
     * Canopus HQX.
     */
    public const VIDEO_HQX = 'hqx';
    /**
     * HuffYUV.
     */
    public const VIDEO_HUFFYUV = 'huffyuv';
    /**
     * id Quake II CIN video (decoders: idcinvideo ).
     */
    public const VIDEO_IDCIN = 'idcin';
    /**
     * iCEDraw text.
     */
    public const VIDEO_IDF = 'idf';
    /**
     * IFF ACBM/ANIM/DEEP/ILBM/PBM/RGB8/RGBN (decoders: iff ).
     */
    public const VIDEO_IFF_ILBM = 'iff_ilbm';
    /**
     * Infinity IMM4.
     */
    public const VIDEO_IMM4 = 'imm4';
    /**
     * Intel Indeo 2.
     */
    public const VIDEO_INDEO2 = 'indeo2';
    /**
     * Intel Indeo 3.
     */
    public const VIDEO_INDEO3 = 'indeo3';
    /**
     * Intel Indeo Video Interactive 4.
     */
    public const VIDEO_INDEO4 = 'indeo4';
    /**
     * Intel Indeo Video Interactive 5.
     */
    public const VIDEO_INDEO5 = 'indeo5';
    /**
     * Interplay MVE video.
     */
    public const VIDEO_INTERPLAYVIDEO = 'interplayvideo';
    /**
     * JPEG 2000 (decoders: jpeg2000 libopenjpeg ) (encoders: jpeg2000 libopenjpeg ).
     */
    public const VIDEO_JPEG2000 = 'jpeg2000';
    /**
     * JPEG-LS.
     */
    public const VIDEO_JPEGLS = 'jpegls';
    /**
     * Bitmap Brothers JV video.
     */
    public const VIDEO_JV = 'jv';
    /**
     * Kega Game Video.
     */
    public const VIDEO_KGV1 = 'kgv1';
    /**
     * Karl Morton's video codec.
     */
    public const VIDEO_KMVC = 'kmvc';
    /**
     * Lagarith lossless.
     */
    public const VIDEO_LAGARITH = 'lagarith';
    /**
     * Lossless JPEG.
     */
    public const VIDEO_LJPEG = 'ljpeg';
    /**
     * LOCO.
     */
    public const VIDEO_LOCO = 'loco';
    /**
     * Matrox Uncompressed SD.
     */
    public const VIDEO_M101 = 'm101';
    /**
     * Electronic Arts Madcow Video (decoders: eamad ).
     */
    public const VIDEO_MAD = 'mad';
    /**
     * MagicYUV video.
     */
    public const VIDEO_MAGICYUV = 'magicyuv';
    /**
     * Sony PlayStation MDEC (Motion DECoder).
     */
    public const VIDEO_MDEC = 'mdec';
    /**
     * Mimic.
     */
    public const VIDEO_MIMIC = 'mimic';
    /**
     * Motion JPEG (encoders: mjpeg mjpeg_vaapi ).
     */
    public const VIDEO_MJPEG = 'mjpeg';
    /**
     * Apple MJPEG-B.
     */
    public const VIDEO_MJPEGB = 'mjpegb';
    /**
     * American Laser Games MM Video.
     */
    public const VIDEO_MMVIDEO = 'mmvideo';
    /**
     * Motion Pixels video.
     */
    public const VIDEO_MOTIONPIXELS = 'motionpixels';
    /**
     * MPEG-1 video (decoders: mpeg1video mpeg1_v4l2m2m ).
     */
    public const VIDEO_MPEG1VIDEO = 'mpeg1video';
    /**
     * MPEG-2 video (decoders: mpeg2video mpegvideo mpeg2_v4l2m2m ) (encoders: mpeg2video mpeg2_vaapi ).
     */
    public const VIDEO_MPEG2VIDEO = 'mpeg2video';
    /**
     * MPEG-4 part 2 (decoders: mpeg4 mpeg4_v4l2m2m ) (encoders: mpeg4 libxvid mpeg4_v4l2m2m ).
     */
    public const VIDEO_MPEG4 = 'mpeg4';
    /**
     * MS ATC Screen.
     */
    public const VIDEO_MSA1 = 'msa1';
    /**
     * Mandsoft Screen Capture Codec.
     */
    public const VIDEO_MSCC = 'mscc';
    /**
     * MPEG-4 part 2 Microsoft variant version 1.
     */
    public const VIDEO_MSMPEG4V1 = 'msmpeg4v1';
    /**
     * MPEG-4 part 2 Microsoft variant version 2.
     */
    public const VIDEO_MSMPEG4V2 = 'msmpeg4v2';
    /**
     * MPEG-4 part 2 Microsoft variant version 3 (decoders: msmpeg4 ) (encoders: msmpeg4 ).
     */
    public const VIDEO_MSMPEG4V3 = 'msmpeg4v3';
    /**
     * Microsoft RLE.
     */
    public const VIDEO_MSRLE = 'msrle';
    /**
     * MS Screen 1.
     */
    public const VIDEO_MSS1 = 'mss1';
    /**
     * MS Windows Media Video V9 Screen.
     */
    public const VIDEO_MSS2 = 'mss2';
    /**
     * Microsoft Video 1.
     */
    public const VIDEO_MSVIDEO1 = 'msvideo1';
    /**
     * LCL (LossLess Codec Library) MSZH.
     */
    public const VIDEO_MSZH = 'mszh';
    /**
     * MS Expression Encoder Screen.
     */
    public const VIDEO_MTS2 = 'mts2';
    /**
     * Silicon Graphics Motion Video Compressor 1.
     */
    public const VIDEO_MVC1 = 'mvc1';
    /**
     * Silicon Graphics Motion Video Compressor 2.
     */
    public const VIDEO_MVC2 = 'mvc2';
    /**
     * MatchWare Screen Capture Codec.
     */
    public const VIDEO_MWSC = 'mwsc';
    /**
     * Mobotix MxPEG video.
     */
    public const VIDEO_MXPEG = 'mxpeg';
    /**
     * NuppelVideo/RTJPEG.
     */
    public const VIDEO_NUV = 'nuv';
    /**
     * Amazing Studio Packed Animation File Video.
     */
    public const VIDEO_PAF_VIDEO = 'paf_video';
    /**
     * PAM (Portable AnyMap) image.
     */
    public const VIDEO_PAM = 'pam';
    /**
     * PBM (Portable BitMap) image.
     */
    public const VIDEO_PBM = 'pbm';
    /**
     * PC Paintbrush PCX image.
     */
    public const VIDEO_PCX = 'pcx';
    /**
     * PGM (Portable GrayMap) image.
     */
    public const VIDEO_PGM = 'pgm';
    /**
     * PGMYUV (Portable GrayMap YUV) image.
     */
    public const VIDEO_PGMYUV = 'pgmyuv';
    /**
     * Pictor/PC Paint.
     */
    public const VIDEO_PICTOR = 'pictor';
    /**
     * Apple Pixlet.
     */
    public const VIDEO_PIXLET = 'pixlet';
    /**
     * PNG (Portable Network Graphics) image.
     */
    public const VIDEO_PNG = 'png';
    /**
     * PPM (Portable PixelMap) image.
     */
    public const VIDEO_PPM = 'ppm';
    /**
     * Apple ProRes (iCodec Pro) (encoders: prores prores_aw prores_ks ).
     */
    public const VIDEO_PRORES = 'prores';
    /**
     * Brooktree ProSumer Video.
     */
    public const VIDEO_PROSUMER = 'prosumer';
    /**
     * Photoshop PSD file.
     */
    public const VIDEO_PSD = 'psd';
    /**
     * V.Flash PTX image.
     */
    public const VIDEO_PTX = 'ptx';
    /**
     * Apple QuickDraw.
     */
    public const VIDEO_QDRAW = 'qdraw';
    /**
     * Q-team QPEG.
     */
    public const VIDEO_QPEG = 'qpeg';
    /**
     * QuickTime Animation (RLE) video.
     */
    public const VIDEO_QTRLE = 'qtrle';
    /**
     * AJA Kona 10-bit RGB Codec.
     */
    public const VIDEO_R10K = 'r10k';
    /**
     * Uncompressed RGB 10-bit.
     */
    public const VIDEO_R210 = 'r210';
    /**
     * RemotelyAnywhere Screen Capture.
     */
    public const VIDEO_RASC = 'rasc';
    /**
     * raw video.
     */
    public const VIDEO_RAWVIDEO = 'rawvideo';
    /**
     * RL2 video.
     */
    public const VIDEO_RL2 = 'rl2';
    /**
     * id RoQ video (decoders: roqvideo ) (encoders: roqvideo ).
     */
    public const VIDEO_ROQ = 'roq';
    /**
     * QuickTime video (RPZA).
     */
    public const VIDEO_RPZA = 'rpza';
    /**
     * innoHeim/Rsupport Screen Capture Codec.
     */
    public const VIDEO_RSCC = 'rscc';
    /**
     * RealVideo 1.0.
     */
    public const VIDEO_RV10 = 'rv10';
    /**
     * RealVideo 2.0.
     */
    public const VIDEO_RV20 = 'rv20';
    /**
     * RealVideo 3.0.
     */
    public const VIDEO_RV30 = 'rv30';
    /**
     * RealVideo 4.0.
     */
    public const VIDEO_RV40 = 'rv40';
    /**
     * LucasArts SANM/SMUSH video.
     */
    public const VIDEO_SANM = 'sanm';
    /**
     * ScreenPressor.
     */
    public const VIDEO_SCPR = 'scpr';
    /**
     * Screenpresso.
     */
    public const VIDEO_SCREENPRESSO = 'screenpresso';
    /**
     * SGI image.
     */
    public const VIDEO_SGI = 'sgi';
    /**
     * SGI RLE 8-bit.
     */
    public const VIDEO_SGIRLE = 'sgirle';
    /**
     * BitJazz SheerVideo.
     */
    public const VIDEO_SHEERVIDEO = 'sheervideo';
    /**
     * Smacker video (decoders: smackvid ).
     */
    public const VIDEO_SMACKVIDEO = 'smackvideo';
    /**
     * QuickTime Graphics (SMC).
     */
    public const VIDEO_SMC = 'smc';
    /**
     * Sigmatel Motion Video.
     */
    public const VIDEO_SMVJPEG = 'smvjpeg';
    /**
     * Snow.
     */
    public const VIDEO_SNOW = 'snow';
    /**
     * Sunplus JPEG (SP5X).
     */
    public const VIDEO_SP5X = 'sp5x';
    /**
     * NewTek SpeedHQ.
     */
    public const VIDEO_SPEEDHQ = 'speedhq';
    /**
     * Screen Recorder Gold Codec.
     */
    public const VIDEO_SRGC = 'srgc';
    /**
     * Sun Rasterfile image.
     */
    public const VIDEO_SUNRAST = 'sunrast';
    /**
     * Scalable Vector Graphics.
     */
    public const VIDEO_SVG = 'svg';
    /**
     * Sorenson Vector Quantizer 1 / Sorenson Video 1 / SVQ1.
     */
    public const VIDEO_SVQ1 = 'svq1';
    /**
     * Sorenson Vector Quantizer 3 / Sorenson Video 3 / SVQ3.
     */
    public const VIDEO_SVQ3 = 'svq3';
    /**
     * Truevision Targa image.
     */
    public const VIDEO_TARGA = 'targa';
    /**
     * Pinnacle TARGA CineWave YUV16.
     */
    public const VIDEO_TARGA_Y216 = 'targa_y216';
    /**
     * TDSC.
     */
    public const VIDEO_TDSC = 'tdsc';
    /**
     * Electronic Arts TGQ video (decoders: eatgq ).
     */
    public const VIDEO_TGQ = 'tgq';
    /**
     * Electronic Arts TGV video (decoders: eatgv ).
     */
    public const VIDEO_TGV = 'tgv';
    /**
     * Theora (encoders: libtheora ).
     */
    public const VIDEO_THEORA = 'theora';
    /**
     * Nintendo Gamecube THP video.
     */
    public const VIDEO_THP = 'thp';
    /**
     * Tiertex Limited SEQ video.
     */
    public const VIDEO_TIERTEXSEQVIDEO = 'tiertexseqvideo';
    /**
     * TIFF image.
     */
    public const VIDEO_TIFF = 'tiff';
    /**
     * 8088flex TMV.
     */
    public const VIDEO_TMV = 'tmv';
    /**
     * Electronic Arts TQI video (decoders: eatqi ).
     */
    public const VIDEO_TQI = 'tqi';
    /**
     * Duck TrueMotion 1.0.
     */
    public const VIDEO_TRUEMOTION1 = 'truemotion1';
    /**
     * Duck TrueMotion 2.0.
     */
    public const VIDEO_TRUEMOTION2 = 'truemotion2';
    /**
     * Duck TrueMotion 2.0 Real Time.
     */
    public const VIDEO_TRUEMOTION2RT = 'truemotion2rt';
    /**
     * TechSmith Screen Capture Codec (decoders: camtasia ).
     */
    public const VIDEO_TSCC = 'tscc';
    /**
     * TechSmith Screen Codec 2.
     */
    public const VIDEO_TSCC2 = 'tscc2';
    /**
     * Renderware TXD (TeXture Dictionary) image.
     */
    public const VIDEO_TXD = 'txd';
    /**
     * IBM UltiMotion (decoders: ultimotion ).
     */
    public const VIDEO_ULTI = 'ulti';
    /**
     * Ut Video.
     */
    public const VIDEO_UTVIDEO = 'utvideo';
    /**
     * Uncompressed 4:2:2 10-bit.
     */
    public const VIDEO_V210 = 'v210';
    /**
     * Uncompressed 4:2:2 10-bit.
     */
    public const VIDEO_V210X = 'v210x';
    /**
     * Uncompressed packed 4:4:4.
     */
    public const VIDEO_V308 = 'v308';
    /**
     * Uncompressed packed QT 4:4:4:4.
     */
    public const VIDEO_V408 = 'v408';
    /**
     * Uncompressed 4:4:4 10-bit.
     */
    public const VIDEO_V410 = 'v410';
    /**
     * Beam Software VB.
     */
    public const VIDEO_VB = 'vb';
    /**
     * VBLE Lossless Codec.
     */
    public const VIDEO_VBLE = 'vble';
    /**
     * SMPTE VC-1 (decoders: vc1 vc1_v4l2m2m ).
     */
    public const VIDEO_VC1 = 'vc1';
    /**
     * Windows Media Video 9 Image v2.
     */
    public const VIDEO_VC1IMAGE = 'vc1image';
    /**
     * ATI VCR1.
     */
    public const VIDEO_VCR1 = 'vcr1';
    /**
     * Miro VideoXL (decoders: xl ).
     */
    public const VIDEO_VIXL = 'vixl';
    /**
     * Sierra VMD video.
     */
    public const VIDEO_VMDVIDEO = 'vmdvideo';
    /**
     * VMware Screen Codec / VMware Video.
     */
    public const VIDEO_VMNC = 'vmnc';
    /**
     * On2 VP3.
     */
    public const VIDEO_VP3 = 'vp3';
    /**
     * On2 VP5.
     */
    public const VIDEO_VP5 = 'vp5';
    /**
     * On2 VP6.
     */
    public const VIDEO_VP6 = 'vp6';
    /**
     * On2 VP6 (Flash version, with alpha channel).
     */
    public const VIDEO_VP6A = 'vp6a';
    /**
     * On2 VP6 (Flash version).
     */
    public const VIDEO_VP6F = 'vp6f';
    /**
     * On2 VP7.
     */
    public const VIDEO_VP7 = 'vp7';
    /**
     * On2 VP8 (decoders: vp8 vp8_v4l2m2m libvpx ) (encoders: libvpx vp8_v4l2m2m vp8_vaapi ).
     */
    public const VIDEO_VP8 = 'vp8';
    /**
     * Google VP9 (decoders: vp9 libvpx-vp9 ) (encoders: libvpx-vp9 vp9_vaapi ).
     */
    public const VIDEO_VP9 = 'vp9';
    /**
     * WinCAM Motion Video.
     */
    public const VIDEO_WCMV = 'wcmv';
    /**
     * WebP (encoders: libwebp_anim libwebp ).
     */
    public const VIDEO_WEBP = 'webp';
    /**
     * Windows Media Video 7.
     */
    public const VIDEO_WMV1 = 'wmv1';
    /**
     * Windows Media Video 8.
     */
    public const VIDEO_WMV2 = 'wmv2';
    /**
     * Windows Media Video 9.
     */
    public const VIDEO_WMV3 = 'wmv3';
    /**
     * Windows Media Video 9 Image.
     */
    public const VIDEO_WMV3IMAGE = 'wmv3image';
    /**
     * Winnov WNV1.
     */
    public const VIDEO_WNV1 = 'wnv1';
    /**
     * AVFrame to AVPacket passthrough.
     */
    public const VIDEO_WRAPPED_AVFRAME = 'wrapped_avframe';
    /**
     * Westwood Studios VQA (Vector Quantized Animation) video (decoders: vqavideo ).
     */
    public const VIDEO_WS_VQA = 'ws_vqa';
    /**
     * Wing Commander III / Xan.
     */
    public const VIDEO_XAN_WC3 = 'xan_wc3';
    /**
     * Wing Commander IV / Xxan.
     */
    public const VIDEO_XAN_WC4 = 'xan_wc4';
    /**
     * eXtended BINary text.
     */
    public const VIDEO_XBIN = 'xbin';
    /**
     * XBM (X BitMap) image.
     */
    public const VIDEO_XBM = 'xbm';
    /**
     * X-face image.
     */
    public const VIDEO_XFACE = 'xface';
    /**
     * XPM (X PixMap) image.
     */
    public const VIDEO_XPM = 'xpm';
    /**
     * XWD (X Window Dump) image.
     */
    public const VIDEO_XWD = 'xwd';
    /**
     * Uncompressed YUV 4:1:1 12-bit.
     */
    public const VIDEO_Y41P = 'y41p';
    /**
     * YUY2 Lossless Codec.
     */
    public const VIDEO_YLC = 'ylc';
    /**
     * Psygnosis YOP Video.
     */
    public const VIDEO_YOP = 'yop';
    /**
     * Uncompressed packed 4:2:0.
     */
    public const VIDEO_YUV4 = 'yuv4';
    /**
     * ZeroCodec Lossless Video.
     */
    public const VIDEO_ZEROCODEC = 'zerocodec';
    /**
     * LCL (LossLess Codec Library) ZLIB.
     */
    public const VIDEO_ZLIB = 'zlib';
    /**
     * Zip Motion Blocks Video.
     */
    public const VIDEO_ZMBV = 'zmbv';
    /**
     * 4GV (Fourth Generation Vocoder).
     */
    public const AUDIO_4GV = '4gv';
    /**
     * 8SVX exponential.
     */
    public const AUDIO_8SVX_EXP = '8svx_exp';
    /**
     * 8SVX fibonacci.
     */
    public const AUDIO_8SVX_FIB = '8svx_fib';
    /**
     * AAC (Advanced Audio Coding) (decoders: aac aac_fixed ).
     */
    public const AUDIO_AAC = 'aac';
    /**
     * AAC LATM (Advanced Audio Coding LATM syntax).
     */
    public const AUDIO_AAC_LATM = 'aac_latm';
    /**
     * ATSC A/52A (AC-3) (decoders: ac3 ac3_fixed ) (encoders: ac3 ac3_fixed ).
     */
    public const AUDIO_AC3 = 'ac3';
    /**
     * ADPCM 4X Movie.
     */
    public const AUDIO_ADPCM_4XM = 'adpcm_4xm';
    /**
     * SEGA CRI ADX ADPCM.
     */
    public const AUDIO_ADPCM_ADX = 'adpcm_adx';
    /**
     * ADPCM Nintendo Gamecube AFC.
     */
    public const AUDIO_ADPCM_AFC = 'adpcm_afc';
    /**
     * ADPCM Yamaha AICA.
     */
    public const AUDIO_ADPCM_AICA = 'adpcm_aica';
    /**
     * ADPCM Creative Technology.
     */
    public const AUDIO_ADPCM_CT = 'adpcm_ct';
    /**
     * ADPCM Nintendo Gamecube DTK.
     */
    public const AUDIO_ADPCM_DTK = 'adpcm_dtk';
    /**
     * ADPCM Electronic Arts.
     */
    public const AUDIO_ADPCM_EA = 'adpcm_ea';
    /**
     * ADPCM Electronic Arts Maxis CDROM XA.
     */
    public const AUDIO_ADPCM_EA_MAXIS_XA = 'adpcm_ea_maxis_xa';
    /**
     * ADPCM Electronic Arts R1.
     */
    public const AUDIO_ADPCM_EA_R1 = 'adpcm_ea_r1';
    /**
     * ADPCM Electronic Arts R2.
     */
    public const AUDIO_ADPCM_EA_R2 = 'adpcm_ea_r2';
    /**
     * ADPCM Electronic Arts R3.
     */
    public const AUDIO_ADPCM_EA_R3 = 'adpcm_ea_r3';
    /**
     * ADPCM Electronic Arts XAS.
     */
    public const AUDIO_ADPCM_EA_XAS = 'adpcm_ea_xas';
    /**
     * G.722 ADPCM (decoders: g722 ) (encoders: g722 ).
     */
    public const AUDIO_ADPCM_G722 = 'adpcm_g722';
    /**
     * G.726 ADPCM (decoders: g726 ) (encoders: g726 ).
     */
    public const AUDIO_ADPCM_G726 = 'adpcm_g726';
    /**
     * G.726 ADPCM little-endian (decoders: g726le ) (encoders: g726le ).
     */
    public const AUDIO_ADPCM_G726LE = 'adpcm_g726le';
    /**
     * ADPCM IMA AMV.
     */
    public const AUDIO_ADPCM_IMA_AMV = 'adpcm_ima_amv';
    /**
     * ADPCM IMA CRYO APC.
     */
    public const AUDIO_ADPCM_IMA_APC = 'adpcm_ima_apc';
    /**
     * ADPCM IMA Eurocom DAT4.
     */
    public const AUDIO_ADPCM_IMA_DAT4 = 'adpcm_ima_dat4';
    /**
     * ADPCM IMA Duck DK3.
     */
    public const AUDIO_ADPCM_IMA_DK3 = 'adpcm_ima_dk3';
    /**
     * ADPCM IMA Duck DK4.
     */
    public const AUDIO_ADPCM_IMA_DK4 = 'adpcm_ima_dk4';
    /**
     * ADPCM IMA Electronic Arts EACS.
     */
    public const AUDIO_ADPCM_IMA_EA_EACS = 'adpcm_ima_ea_eacs';
    /**
     * ADPCM IMA Electronic Arts SEAD.
     */
    public const AUDIO_ADPCM_IMA_EA_SEAD = 'adpcm_ima_ea_sead';
    /**
     * ADPCM IMA Funcom ISS.
     */
    public const AUDIO_ADPCM_IMA_ISS = 'adpcm_ima_iss';
    /**
     * ADPCM IMA Dialogic OKI.
     */
    public const AUDIO_ADPCM_IMA_OKI = 'adpcm_ima_oki';
    /**
     * ADPCM IMA QuickTime.
     */
    public const AUDIO_ADPCM_IMA_QT = 'adpcm_ima_qt';
    /**
     * ADPCM IMA Radical.
     */
    public const AUDIO_ADPCM_IMA_RAD = 'adpcm_ima_rad';
    /**
     * ADPCM IMA Loki SDL MJPEG.
     */
    public const AUDIO_ADPCM_IMA_SMJPEG = 'adpcm_ima_smjpeg';
    /**
     * ADPCM IMA WAV.
     */
    public const AUDIO_ADPCM_IMA_WAV = 'adpcm_ima_wav';
    /**
     * ADPCM IMA Westwood.
     */
    public const AUDIO_ADPCM_IMA_WS = 'adpcm_ima_ws';
    /**
     * ADPCM Microsoft.
     */
    public const AUDIO_ADPCM_MS = 'adpcm_ms';
    /**
     * ADPCM MTAF.
     */
    public const AUDIO_ADPCM_MTAF = 'adpcm_mtaf';
    /**
     * ADPCM Playstation.
     */
    public const AUDIO_ADPCM_PSX = 'adpcm_psx';
    /**
     * ADPCM Sound Blaster Pro 2-bit.
     */
    public const AUDIO_ADPCM_SBPRO_2 = 'adpcm_sbpro_2';
    /**
     * ADPCM Sound Blaster Pro 2.6-bit.
     */
    public const AUDIO_ADPCM_SBPRO_3 = 'adpcm_sbpro_3';
    /**
     * ADPCM Sound Blaster Pro 4-bit.
     */
    public const AUDIO_ADPCM_SBPRO_4 = 'adpcm_sbpro_4';
    /**
     * ADPCM Shockwave Flash.
     */
    public const AUDIO_ADPCM_SWF = 'adpcm_swf';
    /**
     * ADPCM Nintendo THP.
     */
    public const AUDIO_ADPCM_THP = 'adpcm_thp';
    /**
     * ADPCM Nintendo THP (Little-Endian).
     */
    public const AUDIO_ADPCM_THP_LE = 'adpcm_thp_le';
    /**
     * LucasArts VIMA audio.
     */
    public const AUDIO_ADPCM_VIMA = 'adpcm_vima';
    /**
     * ADPCM CDROM XA.
     */
    public const AUDIO_ADPCM_XA = 'adpcm_xa';
    /**
     * ADPCM Yamaha.
     */
    public const AUDIO_ADPCM_YAMAHA = 'adpcm_yamaha';
    /**
     * ALAC (Apple Lossless Audio Codec).
     */
    public const AUDIO_ALAC = 'alac';
    /**
     * AMR-NB (Adaptive Multi-Rate NarrowBand) (decoders: amrnb libopencore_amrnb ) (encoders: libopencore_amrnb ).
     */
    public const AUDIO_AMR_NB = 'amr_nb';
    /**
     * AMR-WB (Adaptive Multi-Rate WideBand) (decoders: amrwb libopencore_amrwb ) (encoders: libvo_amrwbenc ).
     */
    public const AUDIO_AMR_WB = 'amr_wb';
    /**
     * Monkey's Audio.
     */
    public const AUDIO_APE = 'ape';
    /**
     * aptX (Audio Processing Technology for Bluetooth).
     */
    public const AUDIO_APTX = 'aptx';
    /**
     * aptX HD (Audio Processing Technology for Bluetooth).
     */
    public const AUDIO_APTX_HD = 'aptx_hd';
    /**
     * ATRAC1 (Adaptive TRansform Acoustic Coding).
     */
    public const AUDIO_ATRAC1 = 'atrac1';
    /**
     * ATRAC3 (Adaptive TRansform Acoustic Coding 3).
     */
    public const AUDIO_ATRAC3 = 'atrac3';
    /**
     * ATRAC3 AL (Adaptive TRansform Acoustic Coding 3 Advanced Lossless).
     */
    public const AUDIO_ATRAC3AL = 'atrac3al';
    /**
     * ATRAC3+ (Adaptive TRansform Acoustic Coding 3+) (decoders: atrac3plus ).
     */
    public const AUDIO_ATRAC3P = 'atrac3p';
    /**
     * ATRAC3+ AL (Adaptive TRansform Acoustic Coding 3+ Advanced Lossless) (decoders: atrac3plusal ).
     */
    public const AUDIO_ATRAC3PAL = 'atrac3pal';
    /**
     * ATRAC9 (Adaptive TRansform Acoustic Coding 9).
     */
    public const AUDIO_ATRAC9 = 'atrac9';
    /**
     * On2 Audio for Video Codec (decoders: on2avc ).
     */
    public const AUDIO_AVC = 'avc';
    /**
     * Bink Audio (DCT).
     */
    public const AUDIO_BINKAUDIO_DCT = 'binkaudio_dct';
    /**
     * Bink Audio (RDFT).
     */
    public const AUDIO_BINKAUDIO_RDFT = 'binkaudio_rdft';
    /**
     * Discworld II BMV audio.
     */
    public const AUDIO_BMV_AUDIO = 'bmv_audio';
    /**
     * Constrained Energy Lapped Transform (CELT).
     */
    public const AUDIO_CELT = 'celt';
    /**
     * codec2 (very low bitrate speech codec).
     */
    public const AUDIO_CODEC2 = 'codec2';
    /**
     * RFC 3389 Comfort Noise.
     */
    public const AUDIO_COMFORTNOISE = 'comfortnoise';
    /**
     * Cook / Cooker / Gecko (RealAudio G2).
     */
    public const AUDIO_COOK = 'cook';
    /**
     * Dolby E.
     */
    public const AUDIO_DOLBY_E = 'dolby_e';
    /**
     * DSD (Direct Stream Digital), least significant bit first.
     */
    public const AUDIO_DSD_LSBF = 'dsd_lsbf';
    /**
     * DSD (Direct Stream Digital), least significant bit first, planar.
     */
    public const AUDIO_DSD_LSBF_PLANAR = 'dsd_lsbf_planar';
    /**
     * DSD (Direct Stream Digital), most significant bit first.
     */
    public const AUDIO_DSD_MSBF = 'dsd_msbf';
    /**
     * DSD (Direct Stream Digital), most significant bit first, planar.
     */
    public const AUDIO_DSD_MSBF_PLANAR = 'dsd_msbf_planar';
    /**
     * Delphine Software International CIN audio.
     */
    public const AUDIO_DSICINAUDIO = 'dsicinaudio';
    /**
     * Digital Speech Standard - Standard Play mode (DSS SP).
     */
    public const AUDIO_DSS_SP = 'dss_sp';
    /**
     * DST (Direct Stream Transfer).
     */
    public const AUDIO_DST = 'dst';
    /**
     * DCA (DTS Coherent Acoustics) (decoders: dca ) (encoders: dca ).
     */
    public const AUDIO_DTS = 'dts';
    /**
     * DV audio.
     */
    public const AUDIO_DVAUDIO = 'dvaudio';
    /**
     * ATSC A/52B (AC-3, E-AC-3).
     */
    public const AUDIO_EAC3 = 'eac3';
    /**
     * EVRC (Enhanced Variable Rate Codec).
     */
    public const AUDIO_EVRC = 'evrc';
    /**
     * FLAC (Free Lossless Audio Codec).
     */
    public const AUDIO_FLAC = 'flac';
    /**
     * G.723.1.
     */
    public const AUDIO_G723_1 = 'g723_1';
    /**
     * G.729.
     */
    public const AUDIO_G729 = 'g729';
    /**
     * DPCM Gremlin.
     */
    public const AUDIO_GREMLIN_DPCM = 'gremlin_dpcm';
    /**
     * GSM.
     */
    public const AUDIO_GSM = 'gsm';
    /**
     * GSM Microsoft variant.
     */
    public const AUDIO_GSM_MS = 'gsm_ms';
    /**
     * IAC (Indeo Audio Coder).
     */
    public const AUDIO_IAC = 'iac';
    /**
     * iLBC (Internet Low Bitrate Codec).
     */
    public const AUDIO_ILBC = 'ilbc';
    /**
     * IMC (Intel Music Coder).
     */
    public const AUDIO_IMC = 'imc';
    /**
     * DPCM Interplay.
     */
    public const AUDIO_INTERPLAY_DPCM = 'interplay_dpcm';
    /**
     * Interplay ACM.
     */
    public const AUDIO_INTERPLAYACM = 'interplayacm';
    /**
     * MACE (Macintosh Audio Compression/Expansion) 3:1.
     */
    public const AUDIO_MACE3 = 'mace3';
    /**
     * MACE (Macintosh Audio Compression/Expansion) 6:1.
     */
    public const AUDIO_MACE6 = 'mace6';
    /**
     * Voxware MetaSound.
     */
    public const AUDIO_METASOUND = 'metasound';
    /**
     * MLP (Meridian Lossless Packing).
     */
    public const AUDIO_MLP = 'mlp';
    /**
     * MP1 (MPEG audio layer 1) (decoders: mp1 mp1float ).
     */
    public const AUDIO_MP1 = 'mp1';
    /**
     * MP2 (MPEG audio layer 2) (decoders: mp2 mp2float ) (encoders: mp2 mp2fixed ).
     */
    public const AUDIO_MP2 = 'mp2';
    /**
     * MP3 (MPEG audio layer 3) (decoders: mp3float mp3 ) (encoders: libmp3lame ).
     */
    public const AUDIO_MP3 = 'mp3';
    /**
     * ADU (Application Data Unit) MP3 (MPEG audio layer 3) (decoders: mp3adufloat mp3adu ).
     */
    public const AUDIO_MP3ADU = 'mp3adu';
    /**
     * MP3onMP4 (decoders: mp3on4float mp3on4 ).
     */
    public const AUDIO_MP3ON4 = 'mp3on4';
    /**
     * MPEG-4 Audio Lossless Coding (ALS) (decoders: als ).
     */
    public const AUDIO_MP4ALS = 'mp4als';
    /**
     * Musepack SV7 (decoders: mpc7 ).
     */
    public const AUDIO_MUSEPACK7 = 'musepack7';
    /**
     * Musepack SV8 (decoders: mpc8 ).
     */
    public const AUDIO_MUSEPACK8 = 'musepack8';
    /**
     * Nellymoser Asao.
     */
    public const AUDIO_NELLYMOSER = 'nellymoser';
    /**
     * Opus (Opus Interactive Audio Codec) (decoders: opus libopus ) (encoders: opus libopus ).
     */
    public const AUDIO_OPUS = 'opus';
    /**
     * Amazing Studio Packed Animation File Audio.
     */
    public const AUDIO_PAF_AUDIO = 'paf_audio';
    /**
     * PCM A-law / G.711 A-law.
     */
    public const AUDIO_PCM_ALAW = 'pcm_alaw';
    /**
     * PCM signed 16|20|24-bit big-endian for Blu-ray media.
     */
    public const AUDIO_PCM_BLURAY = 'pcm_bluray';
    /**
     * PCM signed 20|24-bit big-endian.
     */
    public const AUDIO_PCM_DVD = 'pcm_dvd';
    /**
     * PCM 16.8 floating point little-endian.
     */
    public const AUDIO_PCM_F16LE = 'pcm_f16le';
    /**
     * PCM 24.0 floating point little-endian.
     */
    public const AUDIO_PCM_F24LE = 'pcm_f24le';
    /**
     * PCM 32-bit floating point big-endian.
     */
    public const AUDIO_PCM_F32BE = 'pcm_f32be';
    /**
     * PCM 32-bit floating point little-endian.
     */
    public const AUDIO_PCM_F32LE = 'pcm_f32le';
    /**
     * PCM 64-bit floating point big-endian.
     */
    public const AUDIO_PCM_F64BE = 'pcm_f64be';
    /**
     * PCM 64-bit floating point little-endian.
     */
    public const AUDIO_PCM_F64LE = 'pcm_f64le';
    /**
     * PCM signed 20-bit little-endian planar.
     */
    public const AUDIO_PCM_LXF = 'pcm_lxf';
    /**
     * PCM mu-law / G.711 mu-law.
     */
    public const AUDIO_PCM_MULAW = 'pcm_mulaw';
    /**
     * PCM signed 16-bit big-endian.
     */
    public const AUDIO_PCM_S16BE = 'pcm_s16be';
    /**
     * PCM signed 16-bit big-endian planar.
     */
    public const AUDIO_PCM_S16BE_PLANAR = 'pcm_s16be_planar';
    /**
     * PCM signed 16-bit little-endian.
     */
    public const AUDIO_PCM_S16LE = 'pcm_s16le';
    /**
     * PCM signed 16-bit little-endian planar.
     */
    public const AUDIO_PCM_S16LE_PLANAR = 'pcm_s16le_planar';
    /**
     * PCM signed 24-bit big-endian.
     */
    public const AUDIO_PCM_S24BE = 'pcm_s24be';
    /**
     * PCM D-Cinema audio signed 24-bit.
     */
    public const AUDIO_PCM_S24DAUD = 'pcm_s24daud';
    /**
     * PCM signed 24-bit little-endian.
     */
    public const AUDIO_PCM_S24LE = 'pcm_s24le';
    /**
     * PCM signed 24-bit little-endian planar.
     */
    public const AUDIO_PCM_S24LE_PLANAR = 'pcm_s24le_planar';
    /**
     * PCM signed 32-bit big-endian.
     */
    public const AUDIO_PCM_S32BE = 'pcm_s32be';
    /**
     * PCM signed 32-bit little-endian.
     */
    public const AUDIO_PCM_S32LE = 'pcm_s32le';
    /**
     * PCM signed 32-bit little-endian planar.
     */
    public const AUDIO_PCM_S32LE_PLANAR = 'pcm_s32le_planar';
    /**
     * PCM signed 64-bit big-endian.
     */
    public const AUDIO_PCM_S64BE = 'pcm_s64be';
    /**
     * PCM signed 64-bit little-endian.
     */
    public const AUDIO_PCM_S64LE = 'pcm_s64le';
    /**
     * PCM signed 8-bit.
     */
    public const AUDIO_PCM_S8 = 'pcm_s8';
    /**
     * PCM signed 8-bit planar.
     */
    public const AUDIO_PCM_S8_PLANAR = 'pcm_s8_planar';
    /**
     * PCM unsigned 16-bit big-endian.
     */
    public const AUDIO_PCM_U16BE = 'pcm_u16be';
    /**
     * PCM unsigned 16-bit little-endian.
     */
    public const AUDIO_PCM_U16LE = 'pcm_u16le';
    /**
     * PCM unsigned 24-bit big-endian.
     */
    public const AUDIO_PCM_U24BE = 'pcm_u24be';
    /**
     * PCM unsigned 24-bit little-endian.
     */
    public const AUDIO_PCM_U24LE = 'pcm_u24le';
    /**
     * PCM unsigned 32-bit big-endian.
     */
    public const AUDIO_PCM_U32BE = 'pcm_u32be';
    /**
     * PCM unsigned 32-bit little-endian.
     */
    public const AUDIO_PCM_U32LE = 'pcm_u32le';
    /**
     * PCM unsigned 8-bit.
     */
    public const AUDIO_PCM_U8 = 'pcm_u8';
    /**
     * PCM Archimedes VIDC.
     */
    public const AUDIO_PCM_VIDC = 'pcm_vidc';
    /**
     * PCM Zork.
     */
    public const AUDIO_PCM_ZORK = 'pcm_zork';
    /**
     * QCELP / PureVoice.
     */
    public const AUDIO_QCELP = 'qcelp';
    /**
     * QDesign Music Codec 2.
     */
    public const AUDIO_QDM2 = 'qdm2';
    /**
     * QDesign Music.
     */
    public const AUDIO_QDMC = 'qdmc';
    /**
     * RealAudio 1.0 (14.4K) (decoders: real_144 ) (encoders: real_144 ).
     */
    public const AUDIO_RA_144 = 'ra_144';
    /**
     * RealAudio 2.0 (28.8K) (decoders: real_288 ).
     */
    public const AUDIO_RA_288 = 'ra_288';
    /**
     * RealAudio Lossless.
     */
    public const AUDIO_RALF = 'ralf';
    /**
     * DPCM id RoQ.
     */
    public const AUDIO_ROQ_DPCM = 'roq_dpcm';
    /**
     * SMPTE 302M.
     */
    public const AUDIO_S302M = 's302m';
    /**
     * SBC (low-complexity subband codec).
     */
    public const AUDIO_SBC = 'sbc';
    /**
     * DPCM Squareroot-Delta-Exact.
     */
    public const AUDIO_SDX2_DPCM = 'sdx2_dpcm';
    /**
     * Shorten.
     */
    public const AUDIO_SHORTEN = 'shorten';
    /**
     * RealAudio SIPR / ACELP.NET.
     */
    public const AUDIO_SIPR = 'sipr';
    /**
     * Smacker audio (decoders: smackaud ).
     */
    public const AUDIO_SMACKAUDIO = 'smackaudio';
    /**
     * SMV (Selectable Mode Vocoder).
     */
    public const AUDIO_SMV = 'smv';
    /**
     * DPCM Sol.
     */
    public const AUDIO_SOL_DPCM = 'sol_dpcm';
    /**
     * Sonic.
     */
    public const AUDIO_SONIC = 'sonic';
    /**
     * Sonic lossless.
     */
    public const AUDIO_SONICLS = 'sonicls';
    /**
     * Speex (decoders: libspeex ) (encoders: libspeex ).
     */
    public const AUDIO_SPEEX = 'speex';
    /**
     * TAK (Tom's lossless Audio Kompressor).
     */
    public const AUDIO_TAK = 'tak';
    /**
     * TrueHD.
     */
    public const AUDIO_TRUEHD = 'truehd';
    /**
     * DSP Group TrueSpeech.
     */
    public const AUDIO_TRUESPEECH = 'truespeech';
    /**
     * TTA (True Audio).
     */
    public const AUDIO_TTA = 'tta';
    /**
     * VQF TwinVQ.
     */
    public const AUDIO_TWINVQ = 'twinvq';
    /**
     * Sierra VMD audio.
     */
    public const AUDIO_VMDAUDIO = 'vmdaudio';
    /**
     * Vorbis (decoders: vorbis libvorbis ) (encoders: vorbis libvorbis ).
     */
    public const AUDIO_VORBIS = 'vorbis';
    /**
     * Wave synthesis pseudo-codec.
     */
    public const AUDIO_WAVESYNTH = 'wavesynth';
    /**
     * WavPack.
     */
    public const AUDIO_WAVPACK = 'wavpack';
    /**
     * Westwood Audio (SND1) (decoders: ws_snd1 ).
     */
    public const AUDIO_WESTWOOD_SND1 = 'westwood_snd1';
    /**
     * Windows Media Audio Lossless.
     */
    public const AUDIO_WMALOSSLESS = 'wmalossless';
    /**
     * Windows Media Audio 9 Professional.
     */
    public const AUDIO_WMAPRO = 'wmapro';
    /**
     * Windows Media Audio 1.
     */
    public const AUDIO_WMAV1 = 'wmav1';
    /**
     * Windows Media Audio 2.
     */
    public const AUDIO_WMAV2 = 'wmav2';
    /**
     * Windows Media Audio Voice.
     */
    public const AUDIO_WMAVOICE = 'wmavoice';
    /**
     * DPCM Xan.
     */
    public const AUDIO_XAN_DPCM = 'xan_dpcm';
    /**
     * Xbox Media Audio 1.
     */
    public const AUDIO_XMA1 = 'xma1';
    /**
     * Xbox Media Audio 2.
     */
    public const AUDIO_XMA2 = 'xma2';
    /**
     * binary data.
     */
    public const DATA_BIN_DATA = 'bin_data';
    /**
     * DVD Nav packet.
     */
    public const DATA_DVD_NAV_PACKET = 'dvd_nav_packet';
    /**
     * SMPTE 336M Key-Length-Value (KLV) metadata.
     */
    public const DATA_KLV = 'klv';
    /**
     * OpenType font.
     */
    public const DATA_OTF = 'otf';
    /**
     * SCTE 35 Message Queue.
     */
    public const DATA_SCTE_35 = 'scte_35';
    /**
     * timed ID3 metadata.
     */
    public const DATA_TIMED_ID3 = 'timed_id3';
    /**
     * TrueType font.
     */
    public const DATA_TTF = 'ttf';
    /**
     * ASS (Advanced SSA) subtitle (decoders: ssa ass ) (encoders: ssa ass ).
     */
    public const SUBTITLE_ASS = 'ass';
    /**
     * DVB subtitles (decoders: dvbsub ) (encoders: dvbsub ).
     */
    public const SUBTITLE_DVB_SUBTITLE = 'dvb_subtitle';
    /**
     * DVB teletext (decoders: libzvbi_teletextdec ).
     */
    public const SUBTITLE_DVB_TELETEXT = 'dvb_teletext';
    /**
     * DVD subtitles (decoders: dvdsub ) (encoders: dvdsub ).
     */
    public const SUBTITLE_DVD_SUBTITLE = 'dvd_subtitle';
    /**
     * EIA-608 closed captions (decoders: cc_dec ).
     */
    public const SUBTITLE_EIA_608 = 'eia_608';
    /**
     * HDMV Presentation Graphic Stream subtitles (decoders: pgssub ).
     */
    public const SUBTITLE_HDMV_PGS_SUBTITLE = 'hdmv_pgs_subtitle';
    /**
     * HDMV Text subtitle.
     */
    public const SUBTITLE_HDMV_TEXT_SUBTITLE = 'hdmv_text_subtitle';
    /**
     * JACOsub subtitle.
     */
    public const SUBTITLE_JACOSUB = 'jacosub';
    /**
     * MicroDVD subtitle.
     */
    public const SUBTITLE_MICRODVD = 'microdvd';
    /**
     * MOV text.
     */
    public const SUBTITLE_MOV_TEXT = 'mov_text';
    /**
     * MPL2 subtitle.
     */
    public const SUBTITLE_MPL2 = 'mpl2';
    /**
     * PJS (Phoenix Japanimation Society) subtitle.
     */
    public const SUBTITLE_PJS = 'pjs';
    /**
     * RealText subtitle.
     */
    public const SUBTITLE_REALTEXT = 'realtext';
    /**
     * SAMI subtitle.
     */
    public const SUBTITLE_SAMI = 'sami';
    /**
     * SubRip subtitle with embedded timing.
     */
    public const SUBTITLE_SRT = 'srt';
    /**
     * SSA (SubStation Alpha) subtitle.
     */
    public const SUBTITLE_SSA = 'ssa';
    /**
     * Spruce subtitle format.
     */
    public const SUBTITLE_STL = 'stl';
    /**
     * SubRip subtitle (decoders: srt subrip ) (encoders: srt subrip ).
     */
    public const SUBTITLE_SUBRIP = 'subrip';
    /**
     * SubViewer subtitle.
     */
    public const SUBTITLE_SUBVIEWER = 'subviewer';
    /**
     * SubViewer v1 subtitle.
     */
    public const SUBTITLE_SUBVIEWER1 = 'subviewer1';
    /**
     * raw UTF-8 text.
     */
    public const SUBTITLE_TEXT = 'text';
    /**
     * Timed Text Markup Language.
     */
    public const SUBTITLE_TTML = 'ttml';
    /**
     * VPlayer subtitle.
     */
    public const SUBTITLE_VPLAYER = 'vplayer';
    /**
     * WebVTT subtitle.
     */
    public const SUBTITLE_WEBVTT = 'webvtt';
    /**
     * XSUB.
     */
    public const SUBTITLE_XSUB = 'xsub';
}
