<?php
/**
 * @package     Helpers - Form Helpers
 * @author      singkek
 * @copyright   Copyright(c) 2019
 * @version     1
 **/

if ( ! function_exists('html_escape'))
{
    /**
     * Returns HTML escaped variable.
     *
     * @param	mixed	$var		The input string or array of strings to be escaped.
     * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
     * @return	mixed			The escaped string or array of strings as a result.
     */
    function html_escape($var, $double_encode = TRUE)
    {
        if (empty($var))
        {
            return $var;
        }

        if (is_array($var))
        {
            foreach (array_keys($var) as $key)
            {
                $var[$key] = html_escape($var[$key], $double_encode);
            }

            return $var;
        }

        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8', $double_encode);
    }
}

if ( ! function_exists('form_dropdown'))
{
    /**
     * function to generate dropdown menu
     *
     * @param	mixed	$data
     * @param	mixed	$options
     * @param	mixed	$selected
     * @param	mixed	$extra
     * @return	string
     */
    function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '')
    {
        $defaults = array();

        if (is_array($data))
        {
            if (isset($data['selected']))
            {
                $selected = $data['selected'];
                unset($data['selected']); // select tags don't have a selected attribute
            }

            if (isset($data['options']))
            {
                $options = $data['options'];
                unset($data['options']); // select tags don't use an options attribute
            }
        }
        else
        {
            $defaults = array('name' => $data);
        }

        is_array($selected) OR $selected = array($selected);
        is_array($options) OR $options = array($options);

        // If no selected state was submitted we will attempt to set it automatically
        if (empty($selected))
        {
            if (is_array($data))
            {
                if (isset($data['name'], $_POST[$data['name']]))
                {
                    $selected = array($_POST[$data['name']]);
                }
            }
            elseif (isset($_POST[$data]))
            {
                $selected = array($_POST[$data]);
            }
        }

        $extra = _attributes_to_string($extra);

        $multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

        $form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

        foreach ($options as $key => $val)
        {
            $key = (string) $key;

            if (is_array($val))
            {
                if (empty($val))
                {
                    continue;
                }

                $form .= '<optgroup label="'.$key."\">\n";

                foreach ($val as $optgroup_key => $optgroup_val)
                {
                    $sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
                    $form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
                        .(string) $optgroup_val."</option>\n";
                }

                $form .= "</optgroup>\n";
            }
            else
            {
                $form .= '<option value="'.html_escape($key).'"'
                    .(in_array($key, $selected) ? ' selected="selected"' : '').'>'
                    .(string) $val."</option>\n";
            }
        }

        return $form."</select>\n";
    }
}

if ( ! function_exists('check_checkbox_filter'))
{
    /**
     * function to check checkbox filter is checked or not
     *
     * @param string $name
     * @param string $allGet
     * @return bool|string
     */
    function check_checkbox_filter($name = '',$allGet = '')
    {
        /*
         * define variable
         */
        $check = false;

        /*
         * check parameter data
         */
        if ($name == '' || $allGet == '')
        {
            return true;
        }

        if (count((array)$allGet) > 0)
        {
            foreach ($allGet as $key => $val)
            {
                if ($key == $key)
                {
                    if($val == '1')
                    {
                        $check = true;
                    }
                }
            }
        }


        if ($check)
        {
            /*
             * if checkbox checked
             */
            return 'checked';
        }
        else
        {
            return false;
        }
    }
}

if ( ! function_exists('anchor'))
{
    /**
     * Anchor Link
     *
     * Creates an anchor based on the local URL.
     *
     * @param	string	the URL
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
    function anchor($uri = '', $title = '', $attributes = '')
    {
        $title = (string) $title;

        $site_url = is_array($uri)
            ? url($uri)
            : (preg_match('#^(\w+:)?//#i', $uri) ? $uri : url($uri));

        if ($title === '')
        {
            $title = $site_url;
        }

        if ($attributes !== '')
        {
            $attributes = _stringify_attributes($attributes);
        }

        return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
    }
}

if ( ! function_exists('mailto'))
{
    /**
     * Mailto Link
     *
     * @param	string	the email address
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
    function mailto($email, $title = '', $attributes = '')
    {
        $title = (string) $title;

        if ($title === '')
        {
            $title = $email;
        }

        return '<a href="mailto:'.$email.'"'._stringify_attributes($attributes).'>'.$title.'</a>';
    }
}

if ( ! function_exists('telto'))
{
    /**
     * telto Link
     *
     * @param	string	the phone number
     * @param	string	the link title
     * @param	mixed	any attributes
     * @return	string
     */
    function telto($email, $title = '', $attributes = '')
    {
        $title = (string) $title;

        if ($title === '')
        {
            $title = $email;
        }

        return '<a href="tel:'.$email.'"'._stringify_attributes($attributes).'>'.$title.'</a>';
    }
}

if ( ! function_exists('img'))
{
    /**
     * Image
     *
     * Generates an <img /> element
     *
     * @param	mixed
     * @param	bool
     * @param	mixed
     * @return	string
     */
    function img($src = '', $attributes = '')
    {
        if ( ! is_array($src) )
        {
            $src = array('src' => $src);
        }

        // If there is no alt attribute defined, set it to an empty string
        if ( ! isset($src['alt']))
        {
            $src['alt'] = '';
        }

        $img = '<img';

        foreach ($src as $k => $v)
        {
            if ($k === 'src' && ! preg_match('#^([a-z]+:)?//#i', $v))
            {
                $img .= ' src="'.url($v).'"';
            }
            else
            {
                $img .= ' '.$k.'="'.$v.'"';
            }
        }

        return $img._stringify_attributes($attributes).' />';
    }
}

if ( ! function_exists('ul'))
{
    /**
     * Unordered List
     *
     * Generates an HTML unordered list from an single or multi-dimensional array.
     *
     * @param	array
     * @param	mixed
     * @return	string
     */
    function ul($list, $attributes = '')
    {
        return _list('ul', $list, $attributes);
    }
}

if ( ! function_exists('ol'))
{
    /**
     * Ordered List
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @param	array
     * @param	mixed
     * @return	string
     */
    function ol($list, $attributes = '')
    {
        return _list('ol', $list, $attributes);
    }
}

if ( ! function_exists('_list'))
{
    /**
     * Generates the list
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @param	string
     * @param	mixed
     * @param	mixed
     * @param	int
     * @return	string
     */
    function _list($type = 'ul', $list = array(), $attributes = '', $depth = 0)
    {
        // If an array wasn't submitted there's nothing to do...
        if ( ! is_array($list))
        {
            return $list;
        }

        // Set the indentation based on the depth
        $out = str_repeat(' ', $depth)
            // Write the opening list tag
            .'<'.$type._stringify_attributes($attributes).">\n";


        // Cycle through the list elements.  If an array is
        // encountered we will recursively call _list()

        static $_last_list_item = '';
        foreach ($list as $key => $val)
        {
            $_last_list_item = $key;

            $out .= str_repeat(' ', $depth + 2).'<li>';

            if ( ! is_array($val))
            {
                $out .= $val;
            }
            else
            {
                $out .= $_last_list_item."\n"._list($type, $val, '', $depth + 4).str_repeat(' ', $depth + 2);
            }

            $out .= "</li>\n";
        }

        // Set the indentation for the closing tag and apply it
        return $out.str_repeat(' ', $depth).'</'.$type.">\n";
    }
}
