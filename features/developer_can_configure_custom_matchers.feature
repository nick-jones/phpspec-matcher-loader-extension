Feature: Developers can configure supplementary custom matchers
  As a developer
  I want to be able to add custom matchers via configuration
  So that I can share them and avoid duplication

  Scenario: Configuring a matcher that implements MatcherInterface directly
    Given the spec file "spec/PhpSpecExtensionTest/MatcherLoader/Ex1/MarkdownSpec.php" contains:
      """
      <?php

      namespace spec\PhpSpecExtensionTest\MatcherLoader\Ex1;

      use PhpSpec\ObjectBehavior;
      use Prophecy\Argument;

      class MarkdownSpec extends ObjectBehavior
      {
          function it_converts_plain_text_to_html_paragraphs()
          {
              $this->toHtml('Hi, there')->shouldReturnHtml();
          }
      }

      """
    And the class file "spec/PhpSpecExtensionTest/MatcherLoader/Ex1/Markdown.php" contains:
      """
      <?php

      namespace PhpSpecExtensionTest\MatcherLoader\Ex1;

      class Markdown
      {
          public function toHtml($argument1)
          {
              return '<html>Hi, there</html>';
          }
      }
      """
    And the class file "spec/PhpSpecExtensionTest/MatcherLoader/CustomerHtmlMatcher.php" contains:
      """
      <?php

      namespace PhpSpecExtensionTest\MatcherLoader;

      use PhpSpec\Matcher\BasicMatcher;

      class CustomerHtmlMatcher extends BasicMatcher
      {
          public function supports($name, $subject, array $arguments)
          {
              return 'returnHtml' === $name
                  && 0 == count($arguments);
          }

          protected function matches($subject, array $arguments)
          {
              return strpos($subject, '<html>') !== false;
          }

          protected function getFailureException($name, $subject, array $arguments)
          {
              return new FailureException();
          }

          protected function getNegativeFailureException($name, $subject, array $arguments)
          {
              return new FailureException();
          }
      }
      """
    And the config file contains:
      """
      matchers:
          - PhpSpecExtensionTest\MatcherLoader\CustomerHtmlMatcher
      extensions:
          - PhpSpecExtension\MatcherLoader\Extension
      """
    When I run phpspec interactively
    Then the suite should pass

  Scenario: Configuring a matcher that implements MatchersProviderInterface
    Given the spec file "spec/PhpSpecExtensionTest/MatcherLoader/Ex2/MarkdownSpec.php" contains:
      """
      <?php

      namespace spec\PhpSpecExtensionTest\MatcherLoader\Ex2;

      use PhpSpec\ObjectBehavior;
      use Prophecy\Argument;

      class MarkdownSpec extends ObjectBehavior
      {
          function it_converts_plain_text_to_html_paragraphs()
          {
              $this->toHtml('Hi, there')->shouldReturnHtml();
          }
      }

      """
    And the class file "spec/PhpSpecExtensionTest/MatcherLoader/Ex2/Markdown.php" contains:
      """
      <?php

      namespace PhpSpecExtensionTest\MatcherLoader\Ex2;

      class Markdown
      {
          public function toHtml($argument1)
          {
              return '<html>Hi, there</html>';
          }
      }
      """
    And the class file "spec/PhpSpecExtensionTest/MatcherLoader/ProvidingHtmlMatcher.php" contains:
      """
      <?php

      namespace PhpSpecExtensionTest\MatcherLoader;

      use PhpSpec\Matcher\MatchersProviderInterface;

      class ProvidingHtmlMatcher implements MatchersProviderInterface
      {
          public function getMatchers()
          {
              return [
                  'returnHtml' => function ($subject) {
                      return strpos($subject, '<html>') !== false;
                  }
              ];
          }
      }
      """
    And the config file contains:
      """
      extensions:
          - PhpSpecExtension\MatcherLoader\Extension
      matchers:
          - PhpSpecExtensionTest\MatcherLoader\ProvidingHtmlMatcher
      """
    When I run phpspec interactively
    Then the suite should pass