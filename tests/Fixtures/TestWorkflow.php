<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Workflow\ActivityStub;
use Workflow\SignalMethod;
use Workflow\Workflow;
use Workflow\WorkflowStub;

final class TestWorkflow extends Workflow
{
    private bool $canceled = false;

    #[SignalMethod]
    public function cancel(): void
    {
        $this->canceled = true;
    }

    public function execute($shouldAssert = false)
    {
        if ($shouldAssert) {
            assert(! $this->canceled);
        }

        $otherResult = yield ActivityStub::make(TestOtherActivity::class, 'other');

        if ($shouldAssert) {
            assert(! $this->canceled);
        }

        $result = yield ActivityStub::make(TestActivity::class);

        yield WorkflowStub::await(fn (): bool => $this->canceled);

        return 'workflow_' . $result . '_' . $otherResult;
    }
}
