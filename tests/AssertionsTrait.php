<?php
namespace Tests;

use JsonSchema\RefResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator as SchemaValidator;
use PHPUnit_Framework_Assert as PHPUnit;

trait AssertionsTrait
{
    public function assertValidSchema($data, $schema, $refResolverPath = null)
    {
        //$retriever = new UriRetriever;
        //$schema = $retriever->retrieve('file://' . base_path('public/json-schema/draft-04/schema'));

        if ($refResolverPath) {
            $retriever = new UriRetriever();
            $refResolver = new RefResolver($retriever);
            $refResolver->resolve($schema, $refResolverPath);
        }

        $validator = new SchemaValidator();
        $validator->check($data, $schema);

        $success = $validator->isValid();

        if (!$success) {
            $messages = [];
            foreach ($validator->getErrors() as $error) {
                $messages[] = sprintf(
                    "[%s] %s",
                    $error['property'],
                    preg_replace("/[\s\r\n]+/", ' ', $error['message'])
                );
            }

            $this->assertTrue($success, join("\n", $messages));
        }
    }
}
