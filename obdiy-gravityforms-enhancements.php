<?php

/*
Plugin Name: OBDIY GravityForms Enhancements
Plugin URI: https://github.com/JasonDodd511/obdiy-gravityforms-enhancements
Description: Plugin to house GravityForms snippets.
Version: 1.1
Author: Jason Dodd
Author URI: https://cambent.com
License: GPL2
GitHub Plugin URI: https://github.com/JasonDodd511/obdiy-gravityforms-enhancements
GitHub Branch:     master
GitHub Languages:
*/

/**
 * GRAVITY FORMS Active Campaign Full Name Field
 *
 * How to use:
 *  When you want to use a single field for full name, add the AC Fullname Field to your form.
 *  You can then set up an Active Campaign feed and map the first and last names to the Active Campaign first and last name fields.
 *  This plugin will use a complex name parsing algorithm to split the name into it's various parts so that first name and last
 *  name are stored in the appropriate fields in Active Campaign.  You can even store the full name in a custom field within AC
 *  for comparison, if desired.
 *
 * By: Jason Dodd
 */

//--
// Add new AC Fullname field type
//--
class GF_Field_AC_Fullname extends GF_Field_Name
{
	public $type = 'ac_fullname';

	public function get_form_editor_button()
	{
		return array(
			'group' => 'advanced_fields',
			'text'  => __('AC Fullname', 'gravityforms')
		);
	}

	public function get_form_editor_field_title()
	{
		return esc_attr__('AC Fullname', 'gravityforms');
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin = $is_entry_detail || $is_form_editor;

		$form_id  = $form['id'];
		$id       = intval( $this->id );
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;

		$size         = $this->size;
		$class_suffix = RG_CURRENT_VIEW == 'entry' ? '_admin' : '';
		$class        = $size . $class_suffix;

		$disabled_text = $is_form_editor ? "disabled='disabled'" : '';
		$class_suffix  = $is_entry_detail ? '_admin' : '';

		$form_sub_label_placement  = rgar( $form, 'subLabelPlacement' );
		$field_sub_label_placement = $this->subLabelPlacement;
		$is_sub_label_above        = $field_sub_label_placement == 'above' || ( empty( $field_sub_label_placement ) && $form_sub_label_placement == 'above' );
		$sub_label_class_attribute = $field_sub_label_placement == 'hidden_label' ? "class='hidden_sub_label screen-reader-text'" : '';

		$prefix = '';
		$first  = '';
		$middle = '';
		$last   = '';
		$suffix = '';
		$full	= '';

		if ( is_array( $value ) ) {
			$prefix = esc_attr( RGForms::get( $this->id . '.2', $value ) );
			$first  = esc_attr( RGForms::get( $this->id . '.3', $value ) );
			$middle = esc_attr( RGForms::get( $this->id . '.4', $value ) );
			$last   = esc_attr( RGForms::get( $this->id . '.6', $value ) );
			$suffix = esc_attr( RGForms::get( $this->id . '.8', $value ) );
			$full 	= esc_attr( RGForms::get( $this->id . '.9', $value ) );
		}

		$prefix_input = GFFormsModel::get_input( $this, $this->id . '.2' );
		$first_input  = GFFormsModel::get_input( $this, $this->id . '.3' );
		$middle_input = GFFormsModel::get_input( $this, $this->id . '.4' );
		$last_input   = GFFormsModel::get_input( $this, $this->id . '.6' );
		$suffix_input = GFFormsModel::get_input( $this, $this->id . '.8' );
		$full_input	  = GFFormsModel::get_input( $this, $this->id . '.9' );

		$first_placeholder_attribute  = GFCommon::get_input_placeholder_attribute( $first_input );
		$middle_placeholder_attribute = GFCommon::get_input_placeholder_attribute( $middle_input );
		$last_placeholder_attribute   = GFCommon::get_input_placeholder_attribute( $last_input );
		$suffix_placeholder_attribute = GFCommon::get_input_placeholder_attribute( $suffix_input );
		$full_placeholder_attribute	  = GFCommon::get_input_placeholder_attribute( $full_input );

		// ARIA labels. Prefix is handled in self::get_name_prefix_field().
		$first_name_aria_label  = esc_attr__( 'First name', 'gravityforms' );
		$middle_name_aria_label = esc_attr__( 'Middle name', 'gravityforms' );
		$last_name_aria_label   = esc_attr__( 'Last name', 'gravityforms' );
		$suffix_aria_label      = esc_attr__( 'Name suffix', 'gravityforms' );
		$full_aria_label		= esc_attr__( 'Full Name', 'gravityforms' );
		$required_attribute     = $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute      = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

		switch ( $this->nameFormat ) {

			case 'advanced' :
			case 'extended' :
				$prefix_tabindex = GFCommon::get_tabindex();
				$first_tabindex  = GFCommon::get_tabindex();
				$middle_tabindex = GFCommon::get_tabindex();
				$last_tabindex   = GFCommon::get_tabindex();
				$suffix_tabindex = GFCommon::get_tabindex();
				$full_tabindex	 = GFCommon::get_tabindex();

				$prefix_sub_label      = rgar( $prefix_input, 'customLabel' ) != '' ? $prefix_input['customLabel'] : gf_apply_filters( array( 'gform_name_prefix', $form_id ), esc_html__( 'Prefix', 'gravityforms' ), $form_id );
				$first_name_sub_label  = rgar( $first_input, 'customLabel' ) != '' ? $first_input['customLabel'] : gf_apply_filters( array( 'gform_name_first', $form_id ), esc_html__( 'First', 'gravityforms' ), $form_id );
				$middle_name_sub_label = rgar( $middle_input, 'customLabel' ) != '' ? $middle_input['customLabel'] : gf_apply_filters( array( 'gform_name_middle', $form_id ), esc_html__( 'Middle', 'gravityforms' ), $form_id );
				$last_name_sub_label   = rgar( $last_input, 'customLabel' ) != '' ? $last_input['customLabel'] : gf_apply_filters( array( 'gform_name_last', $form_id ), esc_html__( 'Last', 'gravityforms' ), $form_id );
				$suffix_sub_label      = rgar( $suffix_input, 'customLabel' ) != '' ? $suffix_input['customLabel'] : gf_apply_filters( array( 'gform_name_suffix', $form_id ), esc_html__( 'Suffix', 'gravityforms' ), $form_id );
				$full_name_sub_label  = rgar( $full_input, 'customLabel' ) != '' ? $full_input['customLabel'] : gf_apply_filters( array( 'gform_name_full', $form_id ), esc_html__( 'Full', 'gravityforms' ), $form_id );

				$prefix_markup = '';
				$first_markup  = '';
				$middle_markup = '';
				$last_markup   = '';
				$suffix_markup = '';
				$full_markup   = '';

				if ( $is_sub_label_above ) {

					$style = ( $is_admin && rgar( $prefix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $prefix_input, 'isHidden' ) ) {
						$prefix_select_class = isset( $prefix_input['choices'] ) && is_array( $prefix_input['choices'] ) ? 'name_prefix_select' : '';
						$prefix_markup       = self::get_name_prefix_field( $prefix_input, $id, $field_id, $prefix, $disabled_text, $prefix_tabindex );
						$prefix_markup       = "<span id='{$field_id}_2_container' class='name_prefix {$prefix_select_class}' {$style}>
                                                    <label for='{$field_id}_2' {$sub_label_class_attribute}>{$prefix_sub_label}</label>
                                                    {$prefix_markup}
                                                  </span>";
					}

					$style = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first' {$style}>
                                                    <label for='{$field_id}_3' {$sub_label_class_attribute}>{$first_name_sub_label}</label>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' aria-label='{$first_name_aria_label}' {$first_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$first_placeholder_attribute}/>
                                                </span>";
					}

					$style = ( $is_admin && ( ! isset( $middle_input['isHidden'] ) || rgar( $middle_input, 'isHidden' ) ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ( isset( $middle_input['isHidden'] ) && $middle_input['isHidden'] == false ) ) {
						$middle_markup = "<span id='{$field_id}_4_container' class='name_middle' {$style}>
                                                    <label for='{$field_id}_4' {$sub_label_class_attribute}>{$middle_name_sub_label}</label>
                                                    <input type='text' name='input_{$id}.4' id='{$field_id}_4' value='{$middle}' aria-label='{$middle_name_aria_label}' {$middle_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$middle_placeholder_attribute}/>
                                                </span>";
					}

					$style = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last' {$style}>
                                                            <label for='{$field_id}_6' {$sub_label_class_attribute}>{$last_name_sub_label}</label>
                                                            <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' aria-label='{$last_name_aria_label}' {$last_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$last_placeholder_attribute}/>
                                                        </span>";
					}

					$style = ( $is_admin && rgar( $suffix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $suffix_input, 'isHidden' ) ) {
						$suffix_select_class = isset( $suffix_input['choices'] ) && is_array( $suffix_input['choices'] ) ? 'name_suffix_select' : '';
						$suffix_markup       = "<span id='{$field_id}_8_container' class='name_suffix {$suffix_select_class}' {$style}>
                                                        <label for='{$field_id}_8' {$sub_label_class_attribute}>{$suffix_sub_label}</label>
                                                        <input type='text' name='input_{$id}.8' id='{$field_id}_8' value='{$suffix}' aria-label='{$suffix_aria_label}' {$suffix_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$suffix_placeholder_attribute}/>
                                                    </span>";
					}
					$style = ( $is_admin && rgar( $full_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $full_input, 'isHidden' ) ) {
						$full_markup = "<span id='{$field_id}_9_container' class='name_full' {$style}>
                                                    <label for='{$field_id}_9' {$sub_label_class_attribute}>{$full_name_sub_label}</label>
                                                    <input type='text' name='input_{$id}.9' id='{$field_id}_9' value='{$full}' aria-label='{$full_name_aria_label}' {$full_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$full_placeholder_attribute}/>
                                                </span>";
					}


				} else {
					$style = ( $is_admin && rgar( $prefix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $prefix_input, 'isHidden' ) ) {
						$prefix_select_class = isset( $prefix_input['choices'] ) && is_array( $prefix_input['choices'] ) ? 'name_prefix_select' : '';
						$prefix_markup       = self::get_name_prefix_field( $prefix_input, $id, $field_id, $prefix, $disabled_text, $prefix_tabindex );
						$prefix_markup       = "<span id='{$field_id}_2_container' class='name_prefix {$prefix_select_class}' {$style}>
                                                    {$prefix_markup}
                                                    <label for='{$field_id}_2' {$sub_label_class_attribute}>{$prefix_sub_label}</label>
                                                  </span>";
					}

					$style = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first' {$style}>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' aria-label='{$first_name_aria_label}' {$first_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$first_placeholder_attribute}/>
                                                    <label for='{$field_id}_3' {$sub_label_class_attribute}>{$first_name_sub_label}</label>
                                                </span>";
					}

					$style = ( $is_admin && ( ! isset( $middle_input['isHidden'] ) || rgar( $middle_input, 'isHidden' ) ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ( isset( $middle_input['isHidden'] ) && $middle_input['isHidden'] == false ) ) {
						$middle_markup = "<span id='{$field_id}_4_container' class='name_middle' {$style}>
                                                    <input type='text' name='input_{$id}.4' id='{$field_id}_4' value='{$middle}' aria-label='{$middle_name_aria_label}' {$middle_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$middle_placeholder_attribute}/>
                                                    <label for='{$field_id}_4' {$sub_label_class_attribute}>{$middle_name_sub_label}</label>
                                                </span>";
					}

					$style = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last' {$style}>
                                                    <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' aria-label='{$last_name_aria_label}' {$last_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$last_placeholder_attribute}/>
                                                    <label for='{$field_id}_6' {$sub_label_class_attribute}>{$last_name_sub_label}</label>
                                                </span>";
					}

					$style = ( $is_admin && rgar( $suffix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $suffix_input, 'isHidden' ) ) {
						$suffix_select_class = isset( $suffix_input['choices'] ) && is_array( $suffix_input['choices'] ) ? 'name_suffix_select' : '';
						$suffix_markup       = "<span id='{$field_id}_8_container' class='name_suffix {$suffix_select_class}' {$style}>
                                                    <input type='text' name='input_{$id}.8' id='{$field_id}_8' value='{$suffix}' aria-label='{$suffix_aria_label}' {$suffix_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$suffix_placeholder_attribute}/>
                                                    <label for='{$field_id}_8' {$sub_label_class_attribute}>{$suffix_sub_label}</label>
                                                </span>";
					}

					$style = ( $is_admin && rgar( $full_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $full_input, 'isHidden' ) ) {
						$full_markup = "<span id='{$field_id}_9_container' class='name_full' {$style}>
                                                    <input type='text' name='input_{$id}.9' id='{$field_id}_9' value='{$full}' aria-label='{$full_name_aria_label}' {$full_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$full_placeholder_attribute}/>
                                                    <label for='{$field_id}_9' {$sub_label_class_attribute}>{$full_name_sub_label}</label>
                                                </span>";
					}

				}
				$css_class = $this->get_css_class();


				return "<div class='ginput_complex{$class_suffix} ginput_container {$css_class} gfield_trigger_change' id='{$field_id}'>
                            {$prefix_markup}
                            {$first_markup}
                            {$middle_markup}
                            {$last_markup}
                            {$suffix_markup}
							{$full_markup}
                        </div>";
			case 'simple' :
				$value                 = esc_attr( $value );
				$class                 = esc_attr( $class );
				$tabindex              = GFCommon::get_tabindex();
				$placeholder_attribute = GFCommon::get_field_placeholder_attribute( $this );

				return "<div class='ginput_container ginput_container_name'>
                                    <input name='input_{$id}' id='{$field_id}' type='text' value='{$value}' class='{$class}' {$tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$placeholder_attribute}/>
                                </div>";
			default :
				$first_tabindex       = GFCommon::get_tabindex();
				$last_tabindex        = GFCommon::get_tabindex();
				$first_name_sub_label = rgar( $first_input, 'customLabel' ) != '' ? $first_input['customLabel'] : gf_apply_filters( array( 'gform_name_first', $form_id ), esc_html__( 'First', 'gravityforms' ), $form_id );
				$last_name_sub_label  = rgar( $last_input, 'customLabel' ) != '' ? $last_input['customLabel'] : gf_apply_filters( array( 'gform_name_last', $form_id ), esc_html__( 'Last', 'gravityforms' ), $form_id );
				if ( $is_sub_label_above ) {
					$first_markup = '';
					$style        = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first' {$style}>
                                                    <label for='{$field_id}_3' {$sub_label_class_attribute}>{$first_name_sub_label}</label>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' aria-label='{$first_name_aria_label}' {$first_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$first_placeholder_attribute}/>
                                                </span>";
					}

					$last_markup = '';
					$style       = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last' {$style}>
                                                <label for='{$field_id}_6' {$sub_label_class_attribute}>" . $last_name_sub_label . "</label>
                                                <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' aria-label='{$last_name_aria_label}' {$last_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$last_placeholder_attribute}/>
                                            </span>";
					}
				} else {
					$first_markup = '';
					$style        = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first' {$style}>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' aria-label='{$first_name_aria_label}' {$first_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$first_placeholder_attribute}/>
                                                    <label for='{$field_id}_3' {$sub_label_class_attribute}>{$first_name_sub_label}</label>
                                               </span>";
					}

					$last_markup = '';
					$style       = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last' {$style}>
                                                    <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' aria-label='{$last_name_aria_label}' {$last_tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$last_placeholder_attribute}/>
                                                    <label for='{$field_id}_6' {$sub_label_class_attribute}>{$last_name_sub_label}</label>
                                                </span>";
					}
				}

				$css_class = $this->get_css_class();

				return "<div class='ginput_complex{$class_suffix} ginput_container {$css_class}' id='{$field_id}'>
                            {$first_markup}
                            {$last_markup}
                            <div class='gf_clear gf_clear_complex'></div>
                        </div>";
		}
	}

	public function get_css_class() {

		$prefix_input = GFFormsModel::get_input( $this, $this->id . '.2' );
		$first_input  = GFFormsModel::get_input( $this, $this->id . '.3' );
		$middle_input = GFFormsModel::get_input( $this, $this->id . '.4' );
		$last_input   = GFFormsModel::get_input( $this, $this->id . '.6' );
		$suffix_input = GFFormsModel::get_input( $this, $this->id . '.8' );
		$full_input   = GFFormsModel::get_input( $this, $this->id . '.9' );

		$css_class = '';
		$visible_input_count = 0;

		if ( $prefix_input && ! rgar( $prefix_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_prefix ';
		} else {
			$css_class .= 'no_prefix ';
		}

		if ( $first_input && ! rgar( $first_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_first_name ';
		} else {
			$css_class .= 'no_first_name ';
		}

		if ( $middle_input && ! rgar( $middle_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_middle_name ';
		} else {
			$css_class .= 'no_middle_name ';
		}

		if ( $last_input && ! rgar( $last_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_last_name ';
		} else {
			$css_class .= 'no_last_name ';
		}

		if ( $suffix_input && ! rgar( $suffix_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_suffix ';
		} else {
			$css_class .= 'no_suffix ';
		}
		if ( $full_input && ! rgar( $full_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_full_name ';
		} else {
			$css_class .= 'no_full_name ';
		}

		$css_class .= "gf_name_has_{$visible_input_count} ginput_container_name ";

		return trim( $css_class );
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( is_array( $value ) ) {
			$prefix = trim( rgget( $this->id . '.2', $value ) );
			$first  = trim( rgget( $this->id . '.3', $value ) );
			$middle = trim( rgget( $this->id . '.4', $value ) );
			$last   = trim( rgget( $this->id . '.6', $value ) );
			$suffix = trim( rgget( $this->id . '.8', $value ) );
			$full	= trim( rgget( $this->id . '.9', $value ) );

			$name = $prefix;
			$name .= ! empty( $name ) && ! empty( $first ) ? " $first" : $first;
			$name .= ! empty( $name ) && ! empty( $middle ) ? " $middle" : $middle;
			$name .= ! empty( $name ) && ! empty( $last ) ? " $last" : $last;
			$name .= ! empty( $name ) && ! empty( $suffix ) ? " $suffix" : $suffix;
			$name .= ! empty( $name ) && ! empty( $full ) ? " $full" : $full;


			$return = $name;
		} else {
			$return = $value;
		}

		if ( $format === 'html' ) {
			$return = esc_html( $return );
		}
		return $return;
	}

	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		if ( absint( $input_id ) == $input_id ) {
			//If field is simple (one input), simply return full content
			$name = rgar( $entry, $input_id );
			if ( ! empty( $name ) ) {
				return $name;
			}

			//Complex field (multiple inputs). Join all pieces and create name
			$prefix = trim( rgar( $entry, $input_id . '.2' ) );
			$first  = trim( rgar( $entry, $input_id . '.3' ) );
			$middle = trim( rgar( $entry, $input_id . '.4' ) );
			$last   = trim( rgar( $entry, $input_id . '.6' ) );
			$suffix = trim( rgar( $entry, $input_id . '.8' ) );
			$full	= trim( rgar( $entry, $input_id . '.9' ) );

			$name = $prefix;
			$name .= ! empty( $name ) && ! empty( $first ) ? ' ' . $first : $first;
			$name .= ! empty( $name ) && ! empty( $middle ) ? ' ' . $middle : $middle;
			$name .= ! empty( $name ) && ! empty( $last ) ? ' ' . $last : $last;
			$name .= ! empty( $name ) && ! empty( $suffix ) ? ' ' . $suffix : $suffix;
			$name .= ! empty( $name ) && ! empty( $full ) ? ' ' . $full : $full;

			return $name;
		} else {

			return rgar( $entry, $input_id );
		}
	}
}

GF_Fields::register( new GF_Field_AC_Fullname() );

// Add some js so that the field displays properly on the admin screens
add_action( 'gform_editor_js_set_default_values', 'ac_fullname_set_defaults' );

function ac_fullname_set_defaults(){
	?>

	case "ac_fullname" :
	if (!field.label){
	field.label = <?php echo json_encode( esc_html__( 'AC Fullname', 'gravityforms' ) ); ?>;
	}

	field.id = parseFloat(field.id);
	field.nameFormat = "advanced";
	field.inputs = GetAdvancedFullNameFieldInputs(field, true, true, true, true, true);

	break;


	<?php
}

// More js so that the field displays properly on the admin screens
add_action( 'gform_editor_js', 'ac_fullname_script' );

function ac_fullname_script(){
	?>
	<script type='text/javascript'>
        function GetAdvancedFullNameFieldInputs(field, prefixHidden, firstHidden, middleHidden, lastHidden, suffixHidden) {
            var prefixInput = new Input(field.id + '.2', <?php echo json_encode( gf_apply_filters( array( 'gform_name_prefix', rgget( 'id' ) ), esc_html__( 'Prefix', 'gravityforms' ), rgget( 'id' ) ) ); ?>);
            prefixInput.choices = GetDefaultPrefixChoices();
            prefixInput.isHidden = prefixHidden;

            var firstInput = new Input(field.id + '.3', <?php echo json_encode( gf_apply_filters( array( 'gform_name_first', rgget( 'id' ) ), esc_html__( 'First', 'gravityforms' ), rgget( 'id' ) ) ); ?>);
            firstInput.isHidden = firstHidden;

            var middleInput = new Input(field.id + '.4', <?php echo json_encode( gf_apply_filters( array( 'gform_name_middle', rgget( 'id' ) ), esc_html__( 'Middle', 'gravityforms' ), rgget( 'id' ) ) ); ?>);
            middleInput.isHidden = middleHidden;

            var lastInput = new Input(field.id + '.6', <?php echo json_encode( gf_apply_filters( array( 'gform_name_last', rgget( 'id' ) ), esc_html__( 'Last', 'gravityforms' ), rgget( 'id' ) ) ); ?>);
            lastInput.isHidden = lastHidden;

            var suffixInput = new Input(field.id + '.8', <?php echo json_encode( gf_apply_filters( array( 'gform_name_suffix', rgget( 'id' ) ), esc_html__( 'Suffix', 'gravityforms' ), rgget( 'id' ) ) ); ?>);
            suffixInput.isHidden = suffixHidden;

            var fullInput = new Input(field.id + '.9', <?php echo json_encode( gf_apply_filters( array( 'gform_name_full', rgget( 'id' ) ), esc_html__( 'Full', 'gravityforms' ), rgget( 'id' ) ) ); ?>);

            prefixInput.inputType = 'radio';

            return [prefixInput, firstInput, middleInput, lastInput, suffixInput, fullInput];
        }
	</script>
	<?php
}

//--
// Parse the name
//--

require( __DIR__.'/include/nameparse.php');

add_filter("gform_pre_submission", "gf_ac_fullname_parse");

function gf_ac_fullname_parse($form)
{
	//Check to see if there's an ac_fullname field, if yes, get id of field
	foreach($form["fields"] as &$field)
	{
		if ($field['type'] == 'ac_fullname')
		{
			$is_ac_fullname = true;
			$ac_fullname_field_id = $field['id'];
			break;
		}
	}
	$input_id = 'input_' . $field['id'] . '_9';

	//Only execute the rest of this code if there's an ac_fullname field
	if ($is_ac_fullname)
	{
		//Find the name in the POST array
		foreach( $_POST as $key=>$value )
		{
			if($key == $input_id )
			{
				$name = $value;
			}
		}

		// Parse the name
		$parsed_name = parse_name($name);

		// Update the first and last name fields
		$_POST["input_{$ac_fullname_field_id}_3"] = $parsed_name['first'];
		$_POST["input_{$ac_fullname_field_id}_6"] = $parsed_name['last'];

	}
}

//--
// Turn on ability to hide field labels via the admin screens
//--
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );


/**
 * GRAVITY FORMS ASSESSMENT ADD ON
 *
 * How to use:
 *  When you want to turn a Gravity Forms form into an assessment:
 *
 *  1) Add an "Assessment" field (An advanced field) to the form.
 *  2) Add response text to the value fields in a series of radio button controls
 *  3) Set up the confirmation to redirect to any page and pass the following as the query string: entry_id={entry_id}
 *  4) Add the following shortcode to the destination page: [assessment_results]
 *
 * By: Jason Dodd
 */

//--
// Add new Assessment Response hidden field type
//--

class GF_Field_Assessment extends GF_Field_Hidden
{
	public $type = 'assessment';
	public function get_form_editor_button()
	{
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title()
		);
	}
	public function get_form_editor_field_title()
	{
		return esc_attr__('Assessment', 'gravityforms');
	}
	public function get_form_editor_field_settings()
	{
		return array(
			'label_setting',
			'admin_label_setting',
			'default_value_textarea_setting'
		);
	}

	public function allow_html()
	{
		return true;
	}
}

GF_Fields::register( new GF_Field_Assessment() );

//--
// Process Asessment Results
//--

add_filter("gform_pre_submission", "gfaa_build_recommendation");

function gfaa_build_recommendation($form)
{
	//Check to see if there's an assessment field, if yes, get id of field
	foreach($form["fields"] as &$field)
	{
		if ($field['type'] == 'assessment')
		{
			$is_assessment = true;
			$assessment_field_id = $field['id'];
			break;
		}
	}
	//Only execute the rest of this code if there's an assessment field
	if ($is_assessment)
	{
		//Create an array out of $_POST
		foreach( $_POST as $key=>$value )
		{
			//Include only input fields in array
			if(strtok($key, '_') == 'input')
			{
				//Extrapolate the question id and include in array
				$field_id = substr( $key, ( $pos = strpos( $key, '_' ) ) === false ? 0 : $pos + 1 );
				$answers[] = array(
					'id' => $field_id,
					'name' => $key,
					'value' => $value,
				);
			}
		}

		//Build response text and save to assessment field
		foreach ( $answers as $answer )
		{

			//Find the radio button choice in the $forms array that corresponds to the answer selected by the user so we can
			// cross reference and get more data about the original question
			foreach ( $form['fields'] as $key => $value )
			{
				If ( $value['id'] == $answer['id'] )
				{
					$question_id = $key;
					break;
				}
			}

			//perform actions only with radio button fields
			if ( $form['fields'][$question_id]['type'] == 'radio' )
			{

				//determine which radio button selection was made
				foreach ( $form['fields'][$question_id]['choices'] as $selection => $selection_details )
				{
					if ( $selection_details['value'] == $answer['value'] )
					{
						$choice_selected = $selection;
						break;
					}
				}
				//Build the Response text and store in (hidden) assessment field
				if ( $answer['value'] <> $form['fields'][$question_id]['choices'][$choice_selected]['text'] )
				{
					$result .= $_POST["{$answer['name']}"];
				}

				//swap text and value for all radio buttons where value and text are not equal
				if ( $form['fields'][$question_id]['choices'][$choice_selected]['text'] <> $answer['value'] )
				{
					$_POST["{$answer['name']}"] = $form['fields'][$question_id]['choices'][$choice_selected]['text'];
				}
			}
		}
		$_POST["input_{$assessment_field_id}"] = $result;
	}
}

//--
// Display results via shortcode
//--

function gfaa_display_assessment_results()
{
	$error_msg = 'Oops!  There was a problem displaying your results.<br/>
	 Please let us know that this happened by <a class="open-popup-link" href="#contact-popup">sending us a message.</a>';

	//Make sure we have an entry_id variable being passed
	if($_GET['entry_id'])
	{
		$lead_id = $_GET['entry_id'];

		//Pull entry record from db
		$lead = RGFormsModel::get_lead( $lead_id );

		//Make sure there's a valid record being returned
		if ($lead && $lead['status'] <> 'trash')
		{

			//Figure out which form was used so we can get more info about the values in the lead array
			$form = GFFormsModel::get_form_meta( $lead['form_id'] );

			//Build a more symantec array so we can pick out the assessment field
			$values = array();
			foreach( $form['fields'] as $field )
			{
				$values[$field['id']] = array(
					'type' => $field['type'],
					'value' => $lead[ $field['id'] ],
				);
			}

			//Find the assessment entry
			foreach( $values as $value )
			{
				if ( $value['type'] == assessment )
				{
					$show_results = $value['value'];
					break;
				}
			}

			//Display any assessment text found, otherwise, show error
			if($show_results)
			{
				return $show_results;
			}
			else
			{
				return $error_msg;
			}
		}
		else
		{
			return $error_msg;
		}
	}
	else
	{
		return $error_msg;
	}

}

add_shortcode( 'assessment_results' , gfaa_display_assessment_results );


/**
 * Allow certain html tags to be stored as field values
 *
 */
add_filter( 'gform_allowable_tags_' . $form_id, 'allow_basic_tags', 10, 3 );

function allow_basic_tags( $allowable_tags ) {
	return '<div><h1><h2><h3><ul><ol><li><p><a><strong><em>';
}

/**
 * Turn on field label visibility setting in gravity forms
 *
 */

add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

/**
 * Shortcode: Get Remaining Entries
 *
 * Displays the number of remaining entries for forms that have entry
 * limits set
 *
 * @param array $atts   Arguments passed to the shortcode. Accepts 'id',
 *                      'format'.  For format, use 'decimal' to change
 *                      thousands separator to a decimal, otherwise will
 *                      be a comma.
 * @return int|null     What is displayed to the user. Number of entries.
 *                      Null if 'id' isn't supplied or isn't valid.
 */
function gfe_get_remaining_entries( $atts ) {
	extract( shortcode_atts( array(
		'id' => false,
		'format' => false
	), $atts ) );
	if( ! $id ) {
		return '';
	}
	$form = RGFormsModel::get_form_meta( $id );
	if( ! rgar( $form, 'limitEntries' ) || ! rgar( $form, 'limitEntriesCount' ) ){
		return '';
	}
	$entry_count = RGFormsModel::get_lead_count( $form['id'], '', null, null, null, null, 'active' );
	$entries_left = rgar( $form, 'limitEntriesCount' ) - $entry_count;
	$output = $entries_left;
	if( $format ) {
		$format = $format == 'decimal' ? '.' : ',';
		$output = number_format( $entries_left, 0, false, $format );
	}
	return $entries_left > 0 ? $output : 0;
}
add_shortcode( 'gfe_entries_remaining', 'gfe_get_remaining_entries' );

/**
 * Changes to various settings within Gravity forms
 *
 */

// Turns on the ability to hide labels in the GF form builder - isn't that great!
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );
