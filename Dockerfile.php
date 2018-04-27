ARG CLI_IMAGE
FROM ${CLI_IMAGE:-builder} as builder

FROM quay.io/dpc_sdp/php

COPY --from=builder /app /app
