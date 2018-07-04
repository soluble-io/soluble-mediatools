#!/bin/bash
# Small script to fetch a static ffmpeg
set -ex

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

URL=https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-64bit-static.tar.xz
FILE=$(basename ${URL})
DIST=${SCRIPT_DIR}/downloads
DISTFILE=${DIST}/${FILE}
DEST=${SCRIPT_DIR}/${FILE%%.*}

[ -d ${DIST} ] || mkdir ${DIST}
[ -d ${DEST} ] || mkdir ${DEST}

if [[ -f ${DISTFILE} ]]; then
  # not first run
  curl -o ${DISTFILE} -z ${DISTFILE} -L ${URL}
else
  # first run
  curl -o ${DISTFILE} -L ${URL}
fi

tar xvJ --strip-components=1 -C ${DEST} -f ${DISTFILE}
