# Using Alpine Linux Instead of Debian

By default, Symfony Docker uses Debian-based FrankenPHP Docker images.
This is the recommended solution.

Alternatively, it's possible to use Alpine-based images, which are smaller but
are known to be slower, and have several known issues.

To switch to Alpine-based images, apply the following changes to the `Dockerfile`,
where `<digest>` is the output of
`docker buildx imagetools inspect <image> --format '{{json .Manifest.Digest}}'`:

<!-- markdownlint-disable MD010 -->

```diff
-FROM dunglas/frankenphp:1-php8.5@sha256:<digest> AS frankenphp_upstream
+FROM dunglas/frankenphp:1-php8.5-alpine@sha256:<digest> AS frankenphp_upstream

-SHELL ["/bin/bash", "-euxo", "pipefail", "-c"]
+SHELL ["/bin/ash", "-eux", "-o", "pipefail", "-c"]

-# hadolint ignore=DL3008
-RUN <<-EOF
-	apt-get update
-	apt-get install -y --no-install-recommends \
-		file \
-		git
+# hadolint ignore=DL3018
+RUN <<-EOF
+	apk add --no-cache \
+		file \
+		git
 	install-php-extensions \

-# hadolint ignore=DL3008,SC3054,DL4006
-RUN <<-'EOF'
-	apt-get update
-	apt-get install -y --no-install-recommends libtree
+# hadolint ignore=DL3018,SC3054,DL4006
+RUN <<-'EOF'
+	apk add --no-cache libtree
 	mkdir -p /tmp/libs
-	BINARIES=(frankenphp php file)
-	for target in $(printf '%s\n' "${BINARIES[@]}" | xargs -I{} which {}) \
+	BINARIES="frankenphp php file"
+	for target in $(printf '%s\n' $BINARIES | xargs -I{} which {}) \

-		libtree -pv "$target" 2>/dev/null | grep -oP '(?:── )\K/\S+(?= \[)' | while IFS= read -r lib; do
+		libtree -pv "$target" 2>/dev/null | sed -n 's/.*── \(\/[^ ]*\) \[.*/\1/p' | while IFS= read -r lib; do

-	rm -rf /var/lib/apt/lists/*

-FROM debian:13-slim@sha256:<digest> AS frankenphp_prod
+FROM alpine:3@sha256:<digest> AS frankenphp_prod

-SHELL ["/bin/bash", "-euxo", "pipefail", "-c"]
+SHELL ["/bin/ash", "-eux", "-o", "pipefail", "-c"]

-COPY --from=frankenphp_prod_builder /usr/lib/file/magic.mgc /usr/lib/file/magic.mgc
+COPY --from=frankenphp_prod_builder /usr/share/misc/magic.mgc /usr/share/misc/magic.mgc
```

<!-- markdownlint-enable MD010 -->
