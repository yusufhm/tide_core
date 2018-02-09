ARG CLI_IMAGE
FROM ${CLI_IMAGE:-builder} as builder

FROM amazeeio/php:7.1-fpm

COPY --from=builder /app /app
# @todo: Remove the line below once settings moved to base images.
COPY --from=builder /bay /bay
