/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';
import './filter-search-block';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata.name, {
	icon: {
		src: <svg viewBox="22.578 108.939 454.518 193.928" xmlns="http://www.w3.org/2000/svg">
				<path class="st0" d="M 435.54 302.001 L 435.54 177.333 L 383.596 264.773 C 381.864 268.237 378.402 269.968 374.072 269.968 L 363.684 269.968 C 360.22 269.968 355.892 268.237 354.161 264.773 L 302.215 177.333 L 302.215 302.001 L 260.66 302.001 L 260.66 126.254 C 260.66 121.925 264.122 118.462 268.452 118.462 L 300.484 118.462 C 303.08 118.462 305.677 120.193 307.41 121.925 L 368.879 221.486 L 430.345 121.925 C 432.078 119.328 434.675 118.462 437.273 118.462 L 469.306 118.462 C 473.634 118.462 477.096 121.925 477.096 126.254 L 477.096 302.001 L 435.54 302.001 Z"/>
				<path class="st1" d="M 172.353 289.014 C 164.561 293.344 156.769 296.806 148.112 299.403 C 139.454 302.001 130.797 302.867 121.274 302.867 C 106.556 302.867 92.703 300.27 80.583 294.209 C 68.462 289.014 58.074 281.224 49.416 272.565 C 40.759 263.908 33.834 253.519 29.504 241.399 C 24.309 229.278 22.578 218.022 22.578 205.902 C 22.578 192.917 25.175 180.796 30.37 169.541 C 35.565 157.42 42.49 147.897 51.147 138.374 C 59.805 129.717 70.195 122.79 82.315 116.731 C 94.436 111.536 107.421 108.939 122.139 108.939 C 136.856 108.939 150.709 111.536 161.963 117.595 C 174.084 123.656 184.473 130.582 193.13 139.241 C 201.788 147.897 208.714 159.153 213.042 170.407 C 218.237 182.527 219.968 193.782 219.968 205.902 C 219.968 218.022 218.237 230.143 213.042 241.399 C 207.848 252.653 201.788 263.042 193.13 270.834 L 214.775 293.344 C 217.373 295.941 215.64 300.27 211.311 300.27 L 187.071 300.27 C 184.473 300.27 181.876 299.403 181.01 297.672 L 172.353 289.014 Z M 121.274 270.834 C 126.469 270.834 131.663 269.968 135.992 269.103 C 140.32 268.237 144.648 266.506 148.112 263.908 L 123.871 238.801 C 121.274 236.204 123.005 231.009 127.333 231.009 L 152.44 231.009 C 153.307 231.009 155.038 231.876 155.904 232.74 L 168.889 245.727 C 172.353 239.668 175.817 233.607 177.548 226.681 C 179.279 219.755 181.01 212.829 181.01 205.902 C 181.01 198.112 179.279 190.32 176.681 181.661 C 174.084 173.869 169.756 166.943 164.561 161.749 C 159.366 156.556 153.307 151.361 145.515 147.031 C 137.723 142.703 129.931 141.838 121.274 141.838 C 111.751 141.838 103.092 143.569 96.167 147.031 C 89.241 150.495 82.315 155.689 77.121 161.749 C 71.926 167.81 68.462 174.735 65.865 181.661 C 63.269 189.453 62.403 197.245 62.403 204.171 C 62.403 211.963 63.269 219.755 66.731 228.412 C 69.328 236.204 72.792 243.13 78.852 248.324 C 84.046 254.385 90.107 258.714 97.898 263.042 C 103.092 268.237 111.751 270.834 121.274 270.834 Z"/>
			</svg>
	},
	supports: {
		align: ["wide", "full"],
		typography: {
			fontSize: true,
			lineHeight: true,
			__experimentalFontFamily: true,
			__experimentalFontWeight: true,
			__experimentalFontStyle: true,
			__experimentalTextTransform: true,
			__experimentalTextDecoration: true,
			__experimentalLetterSpacing: true,
			__experimentalDefaultControls: {
				fontSize: true
			}
		},
		color: {
			background: true,
			text: true,
			link: true,
		},
		dimensions: false,
		spacing: {
			padding: true,
			margin: true,
			blockGap: false,
		},
	},
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
} );
