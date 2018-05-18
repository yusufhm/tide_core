ARG CLI_IMAGE
FROM ${CLI_IMAGE:-builder} as builder

FROM singledigital/bay-php:latest

COPY --from=builder /app /app
