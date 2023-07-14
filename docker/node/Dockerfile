FROM node:10-alpine


RUN apk add --no-cache su-exec && \
    addgroup bar && \
    adduser -D -h /home -s /bin/sh -G bar foo


RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		g++ \
		gcc \
		make \
	;

ADD entrypoint.sh /entrypoint

ENTRYPOINT ["/entrypoint"]
