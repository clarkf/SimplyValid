quality: mess style test

test:
	@./vendor/bin/phpunit

test-cov:
	@./vendor/bin/phpunit --coverage-html=cov/

mess:
	@./vendor/bin/phpmd \
		src \
		text \
		codesize,design,naming

style:
	@./vendor/bin/phpcs \
		--standard=PSR2 \
		src

.PHONY: quality test test-cov mess style
