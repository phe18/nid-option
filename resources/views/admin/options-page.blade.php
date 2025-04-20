<div class="wrap sage-theme-options">
  <h1>{{ __('Theme Options', 'sage-theme-options') }}</h1>

  <div class="notice notice-success settings-updated is-dismissible" style="display:none;">
      <p>{{ __('Options saved!', 'sage-theme-options') }}</p>
  </div>

  <div class="settings-error notice error is-dismissible" style="display:none;">
      <p class="error-message"></p>
  </div>

  <div class="theme-options-container">
      <div class="options-header">
          <ul class="options-tabs">
              @foreach(config('theme-options.tabs') as $key => $label)
              <li>
                  <a href="#tab-{{ $key }}" class="{{ $loop->first ? 'active' : '' }}">{{ $label }}</a>
              </li>
              @endforeach
          </ul>
          <div class="save-container">
              <div class="spinner"></div>
              <button class="button button-primary save-options">{{ __('Save Changes', 'sage-theme-options') }}</button>
          </div>
      </div>

      <form id="theme-options-form" method="post">
          @php
              $options = get_option('sage_theme_options', []);
          @endphp

          <div class="options-content">
              <!-- General Tab -->
              <div id="tab-general" class="tab-content {{ $loop->first ? 'active' : '' }}">
                  <div class="option-group">
                      <h3>{{ __('Logo', 'sage-theme-options') }}</h3>
                      <div class="media-uploader" data-type="logo">
                          <div class="preview">
                              @if(!empty($options['logo']['url']))
                                  <img src="{{ $options['logo']['url'] }}" alt="Logo Preview">
                              @endif
                          </div>
                          <input type="hidden" name="logo_id" value="{{ $options['logo']['id'] ?? '' }}">
                          <input type="hidden" name="logo_url" value="{{ $options['logo']['url'] ?? '' }}">
                          <button class="button upload-button">{{ __('Upload Logo', 'sage-theme-options') }}</button>
                          <button class="button remove-button" {{ empty($options['logo']['url']) ? 'style="display:none;"' : '' }}>{{ __('Remove', 'sage-theme-options') }}</button>
                      </div>
                  </div>

                  <div class="option-group">
                      <h3>{{ __('Favicon', 'sage-theme-options') }}</h3>
                      <div class="media-uploader" data-type="favicon">
                          <div class="preview">
                              @if(!empty($options['favicon']['url']))
                                  <img src="{{ $options['favicon']['url'] }}" alt="Favicon Preview">
                              @endif
                          </div>
                          <input type="hidden" name="favicon_id" value="{{ $options['favicon']['id'] ?? '' }}">
                          <input type="hidden" name="favicon_url" value="{{ $options['favicon']['url'] ?? '' }}">
                          <button class="button upload-button">{{ __('Upload Favicon', 'sage-theme-options') }}</button>
                          <button class="button remove-button" {{ empty($options['favicon']['url']) ? 'style="display:none;"' : '' }}>{{ __('Remove', 'sage-theme-options') }}</button>
                          <p class="description">{{ __('Recommended size: 32x32 or 16x16 pixels.', 'sage-theme-options') }}</p>
                      </div>
                  </div>

                  <div class="option-group">
                      <h3>{{ __('Heading 1', 'sage-theme-options') }}</h3>
                      <textarea name="theme_options[heading_one]" rows="3" class="large-text">{{ $options['heading_one'] ?? '' }}</textarea>
                      <p class="description">{{ __('Enter your main heading text.', 'sage-theme-options') }}</p>
                  </div>
              </div>

              <!-- SEO Tab -->
              <div id="tab-seo" class="tab-content">
                  <div class="option-group">
                      <h3>{{ __('SEO Content', 'sage-theme-options') }}</h3>
                      @php
                          $seo_content = $options['seo_content'] ?? '';
                          wp_editor($seo_content, 'theme_options_seo_content', [
                              'textarea_name' => 'theme_options[seo_content]',
                              'media_buttons' => true,
                              'textarea_rows' => 10,
                          ]);
                      @endphp
                      <p class="description">{{ __('Enter your SEO content here.', 'sage-theme-options') }}</p>
                  </div>
              </div>

              <!-- Ads Tab -->
              <div id="tab-ads" class="tab-content">
                  <div class="option-group">
                      <h3>{{ __('Header Ads', 'sage-theme-options') }}</h3>
                      <textarea name="theme_options[header_ads]" rows="5" class="large-text code">{{ $options['header_ads'] ?? '' }}</textarea>
                      <p class="description">{{ __('Enter your header advertisement code.', 'sage-theme-options') }}</p>
                  </div>

                  <div class="option-group">
                      <h3>{{ __('Underplay Ads', 'sage-theme-options') }}</h3>
                      <textarea name="theme_options[underplay_ads]" rows="5" class="large-text code">{{ $options['underplay_ads'] ?? '' }}</textarea>
                      <p class="description">{{ __('Enter your underplay advertisement code.', 'sage-theme-options') }}</p>
                  </div>

                  <div class="option-group">
                      <h3>{{ __('Footer Ads', 'sage-theme-options') }}</h3>
                      <textarea name="theme_options[footer_ads]" rows="5" class="large-text code">{{ $options['footer_ads'] ?? '' }}</textarea>
                      <p class="description">{{ __('Enter your footer advertisement code.', 'sage-theme-options') }}</p>
                  </div>
              </div>

              <!-- Scripts Tab -->
              <div id="tab-scripts" class="tab-content">
                  <div class="option-group">
                      <h3>{{ __('Google Site Verification', 'sage-theme-options') }}</h3>
                      <textarea name="theme_options[google_verification]" rows="3" class="large-text code">{{ $options['google_verification'] ?? '' }}</textarea>
                      <p class="description">{{ __('Enter your Google site verification code.', 'sage-theme-options') }}</p>
                  </div>

                  <div class="option-group">
                      <h3>{{ __('Other Scripts', 'sage-theme-options') }}</h3>
                      <textarea name="theme_options[other_scripts]" rows="8" class="large-text code">{{ $options['other_scripts'] ?? '' }}</textarea>
                      <p class="description">{{ __('Enter any additional scripts you want to add to the header.', 'sage-theme-options') }}</p>
                  </div>
              </div>

              <!-- Footer Tab -->
              <div id="tab-footer" class="tab-content">
                  <div class="option-group">
                      <h3>{{ __('Footer Content', 'sage-theme-options') }}</h3>
                      @php
                          $footer_content = $options['footer_content'] ?? '';
                          wp_editor($footer_content, 'theme_options_footer_content', [
                              'textarea_name' => 'theme_options[footer_content]',
                              'media_buttons' => true,
                              'textarea_rows' => 8,
                          ]);
                      @endphp
                      <p class="description">{{ __('Enter your footer content here.', 'sage-theme-options') }}</p>
                  </div>
              </div>
          </div>
      </form>
  </div>
</div>
