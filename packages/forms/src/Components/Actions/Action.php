<?php

namespace Filament\Forms\Components\Actions;

use Filament\Actions\Concerns\HasMountableArguments;
use Filament\Actions\MountableAction;
use Illuminate\Support\Js;
use ReflectionParameter;

class Action extends MountableAction
{
    use Concerns\BelongsToComponent;
    use HasMountableArguments;

    protected function setUp(): void
    {
        parent::setUp();

        $this->iconButton();
    }

    public function getLivewireCallActionName(): string
    {
        return 'callMountedFormComponentAction';
    }

    public function getLivewireMountAction(): ?string
    {
        if (! $this->isMountedOnClick()) {
            return null;
        }

        if ($this->getUrl()) {
            return null;
        }

        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
            $argumentsParameter .= '';
        }

        return "mountFormComponentAction('{$this->getComponent()->getKey()}', '{$this->getName()}'{$argumentsParameter})";
    }

    public function toFormComponent(): ActionContainer
    {
        return ActionContainer::make($this);
    }

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'component' => $this->getComponent(),
            'context', 'operation' => $this->getComponent()->getContainer()->getOperation(),
            'get' => $this->getComponent()->getGetCallback(),
            'model' => $this->getComponent()->getModel(),
            'record' => $this->getComponent()->getRecord(),
            'set' => $this->getComponent()->getSetCallback(),
            'state' => $this->getComponent()->getState(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }
}
