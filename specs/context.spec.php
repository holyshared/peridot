<?php

use Peridot\Runner\Context;

describe('Context', function() {

    beforeEach(function() {
        $this->reflection = new ReflectionClass('Peridot\Runner\Context');
        $context = $this->reflection->newInstanceWithoutConstructor();
        $construct = $this->reflection->getConstructor();
        $construct->setAccessible(true);
        $construct->invoke($context);
        $this->context = $context;
    });

    describe('->addSuite()', function() {
        it("should allow nesting of suites", function() {
            $context = $this->context;
            $child = null;
            $parent = $context->addSuite('Parent suite', function() use ($context, &$child) {
                $child = $context->addSuite('Child suite', function() use ($context) {
                });
            });
            $tests = $parent->getTests();
            assert($tests[0] === $child, "child should have been added to parent");
        });

        it("should allow sibling suites", function() {
            $sibling1 = $this->context->addSuite('Sibling1 suite', function() {});
            $sibling2 = $this->context->addSuite('Sibling2 suite', function() {});
            $tests = $this->context->getCurrentSuite()->getTests();
            assert($tests[0] === $sibling1, "sibling1 should have been added to parent");
            assert($tests[1] === $sibling2, "sibling2 should have been added to parent");
        });

        it("should set pending if value is not null", function() {
            $suite = $this->context->addSuite("desc", function() {}, true);
            assert($suite->getPending(), "suite should be pending");
        });

        it("should ignore pending if value is null", function() {
            $suite = $this->context->addSuite("desc", function() {});
            assert(is_null($suite->getPending()), "pending status should be null");
        });

        it("should execute a suites bound definition", function() {
            $suite = $this->context->addSuite("desc", function() {
                $this->value = "hello";
            });
            $scope = $suite->getScope();
            assert($scope->value == "hello", "value should be bound to suites scope");
        });
    });

    describe("->addTest()", function() {
        it("should set pending status if value is not null", function() {
            $test = $this->context->addTest("is a spec", function() {}, true);
            assert($test->getPending(), "pending status should be true");
        });

        it("should ignore pending status if value is null", function() {
            $test = $this->context->addTest("is a spec", function() {});
            assert(is_null($test->getPending()), "pending status should be null");
        });

        it("should set pending to true if definition is null", function() {
            $test = $this->context->addTest("is a spec");
            assert($test->getPending(), "pending status should be true");
        });
    });

    describe('->addSetupFunction()', function() {
        it('should register an beforeEach callback on the current suite', function() {
            $before = function() {
                return "result";
            };
            $this->context->addSetupFunction($before);
            $result = call_user_func($this->context->getCurrentSuite()->getSetupFunctions()[0]);
            assert("result" === $result, "expected addSetupFunction to register setup function");
        });
    });

    describe('->addTearDownFunction()', function() {
       it('should register an afterEach callback on the current suite', function() {
           $after = function() {
               return "result";
           };
           $this->context->addTearDownFunction($after);
           $result = call_user_func($this->context->getCurrentSuite()->getTearDownFunctions()[0]);
           assert("result" === $result, "expected addSetupFunction to register tear down function");
       });
    });

    describe('::getInstance()', function() {

        beforeEach(function() {
            $this->property = $this->reflection->getProperty('instance');
            $this->property->setAccessible(true);
            $this->previous = $this->property->getValue();
            $this->property->setValue(null);
        });

        afterEach(function() {
            $this->property->setValue($this->previous);
        });

        it("should return a singleton instance of Context", function() {
            $context = Context::getInstance();
            assert($context instanceof Context, "getInstance should return a Context");
        });
    });
});
