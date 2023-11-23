<?php // phpcs:ignore

namespace Jcore\Ydin\Settings;

use WP_Customize_Control;
use WP_Customize_Manager;

/**
 * A custom Color Control component.
 *
 * @since 3.0.0
 */
class ColorControl extends WP_Customize_Control {
	/**
	 * Initialize the control.
	 *
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id Control ID.
	 * @param array                $args Control arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$this->type    = 'jcore-color';
		$this->choices = Customizer::get_color_choices();
	}

	/**
	 * Render the control's content.
	 *
	 * @return void
	 */
	final protected function render_content(): void {
		if ( empty( $this->choices ) ) {
			return;
		}
		$input_id       = '_customize-input-' . $this->id;
		$description_id = '_customize-description-' . $this->id;
		if ( ! empty( $this->label ) ) {
			echo '<label for="' . esc_attr( $input_id ) . '" class="customize-control-title">';
			echo esc_html( $this->label );
			echo '</label>';
		}
		if ( ! empty( $this->description ) ) {
			echo '<span id="' . esc_attr( $description_id ) . '" class="description customize-control-description">';
			echo esc_html( $this->description );
			echo '</span>';
		}
		foreach ( $this->choices as $key => $value ) {
			$checked = $this->value() === $key ? ' checked ' : ' ';
			$entry   = $this->id . '_' . $key;
			echo '<input type="radio" id="' . esc_attr( $entry ) . '" name="' . esc_attr( $this->id ) . '" value="' . esc_attr( $key ) . '" ' . esc_attr( $checked );
			$this->link();
			echo '>';
			echo '<label class="color-dot has-' . esc_attr( $key ) . '-background-color" for="' . esc_attr( $entry ) . '" title="' . esc_attr( $value ) . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="6 6 12 12" width="20" height="20" fill="#000" focusable="false"><path d="M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"></path></svg></label>';
		}
	}
}
