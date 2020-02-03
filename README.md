
# Sculpin Execute Bundle

The bundle adds a `execute` Twig filter to execute external commands and
embed their output to the renderer output.

## Installation

The installation procedure is the same as other Sculpin bundles. Create a
custom Sculpin kernel and register this bundle in the
`getAdditionalSculpinBundles` method.

```php

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;
use Kmchan\Sculpin\ExecuteBundle\SculpinExecuteBundle;

class SculpinKernel extends AbstractKernel
{
	protected function getAdditionalSculpinBundles(): array
	{
		return [
			// some sculpin bundles...
			SculpinExecuteBundle::class,
			// more sculpin bundles...
		];
	}
}

```
## Usage

After installing the bundle, a new `execute` filter is avaiable in Twig. The
filter can be invoked like this:

```twig

{{ input|execute("cat", on_success="stdout", on_failure="exception") }}

```

In the example above, the command to be executed will be `cat`. After the
command is started, the new process will receive the content of variable
`input` from its standard input stream. Later, if the process terminates with
zero exit code, the action specified by `on_success` argument, `stdout` in
this example, will be performed; if the exit code is non-zero instead, the
action specified by `on_failure` argument, `exception` in this case, will
be performed.

Action is specified as a string. It consists of action type, optionally
followed by `:` separator and then its parameter.

There are a few recognized action type. The `stdout` action returns output
messages from the standard output stream. The `stderr` action returns output
messages from the standard error stream. The `text` action returns hardcoded
text message given by the parameter. The `exception` action throws an
exception; its message can be customized by the optional parameter.

Tip: the filter can be invoked by `apply` tag to make the filter "blocky"
like this:

```twig

{% apply execute("cat", on_success="stdout", on_failure="exception") -%}
{% autoescape false %}
data to be sent to the command
more data to be sent to the command
blah blah blah...
{% endautoescape -%}
{% endapply %}

```

## Configuration

The bundle can be configured to control the environment variable of the
created processes like this:

```yaml

sculpin_execute:
	environment:
		- { name: "VAR1", value: "value1" }
		- { name: "VAR2", value: false }

```

In the example above, environment variable VAR1 is set to `value`; on the
other hand, environment variable VAR2 is removed altogether.

## Security

The bundle assumes all commands and inputs are **trusted**, which should be
the usual case for static site generation. Please use another bundle if it
is not the case.

