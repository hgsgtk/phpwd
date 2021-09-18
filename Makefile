.PHONY: help run-tests set-local

help: ## Show options
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

run-tests:
	docker-compose run phpwd composer run-script tests

set-local: ## Build docker image to setup your local docker environment
	docker-compose build
