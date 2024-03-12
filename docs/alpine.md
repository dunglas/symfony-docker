# Using Alpine Linux Instead of Debian

By default, Symfony Docker uses Debian-based FrankenPHP Docker images.
This is the recommended solution.

Alternatively, it's possible to use Alpine-based images, which are smaller but
are known to be slower, and have several known issues.

To switch to Alpine-based images, apply the following changes to the `Dockerfile`:

```patch
-FROM dunglas/frankenphp:1-php8.3 AS frankenphp_upstream
+FROM dunglas/frankenphp:1-alpine-php8.3 AS frankenphp_upstream

-# hadolint ignore=DL3008
-RUN apt-get update && apt-get install -y --no-install-recommends \
-	acl \
-	file \
-	gettext \
-	git \
-	&& rm -rf /var/lib/apt/lists/*
+# hadolint ignore=DL3018
+RUN apk add --no-cache \
+		acl \
+		file \
+		gettext \
+		git \
+	;
```
