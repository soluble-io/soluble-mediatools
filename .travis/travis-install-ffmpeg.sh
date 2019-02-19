#!/bin/bash
# Small script to fetch a static ffmpeg
set -ex

BASE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

URL=https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz
FILE=$(basename ${URL})
DOWNLOAD_DIR=${BASE_DIR}/downloads
DISTFILE=${DOWNLOAD_DIR}/${FILE}
DEST=${BASE_DIR}/${FILE%%.*}

[ -d ${DOWNLOAD_DIR} ] || mkdir ${DOWNLOAD_DIR}
[ -d ${DEST} ] || mkdir ${DEST}

if [[ -f ${DISTFILE} ]]; then
  # not first run
  # curl -o ${DISTFILE} -z ${DISTFILE} -L ${URL}
  echo "Already downloaded";
else
  # first run
  curl -o ${DISTFILE} -L ${URL}
fi

tar xvJ --strip-components=1 -C ${DEST} -f ${DISTFILE}
