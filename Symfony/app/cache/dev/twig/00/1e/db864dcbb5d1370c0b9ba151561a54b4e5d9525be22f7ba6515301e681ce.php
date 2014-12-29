<?php

/* ProjetBDDGeneralBundle:Default:index.html.twig */
class __TwigTemplate_001edb864dcbb5d1370c0b9ba151561a54b4e5d9525be22f7ba6515301e681ce extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "coucou

";
        // line 3
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["result"]) ? $context["result"] : $this->getContext($context, "result")));
        foreach ($context['_seq'] as $context["_key"] => $context["c"]) {
            echo "\t
\t";
            // line 4
            echo twig_escape_filter($this->env, $this->getAttribute($context["c"], "nomConcept", array()), "html", null, true);
            echo "
\t";
            // line 5
            echo twig_escape_filter($this->env, $this->getAttribute($context["c"], "description", array()), "html", null, true);
            echo "
\t";
            // line 6
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["c"], "generalise", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["g"]) {
                // line 7
                echo "\t\t";
                echo twig_escape_filter($this->env, $context["g"], "html", null, true);
                echo "
\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['g'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['c'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "ProjetBDDGeneralBundle:Default:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 7,  37 => 6,  33 => 5,  29 => 4,  23 => 3,  19 => 1,);
    }
}
