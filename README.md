# Syncweb Content Generator

A simple WordPress plugin that adds an **“AI”** button next to text fields in the WordPress admin (including Elementor) and allows you to quickly generate content using the OpenAI/ChatGPT API. This saves time when creating or editing content by letting you generate AI-based text without leaving your WordPress editor.

## Features

- **Easy Setup**: Enter your OpenAI API key in plugin settings and you’re ready to generate content.
- **Inline AI Button**: Injects an “AI” button next to text fields and textareas in the WP admin (e.g., Elementor fields, post editing screen).
- **Customizable**: Adjust the script to limit where you see the AI button (e.g., only in Elementor, only on certain screens, etc.).
- **AJAX-Driven**: Uses WordPress AJAX for secure server-side requests to the ChatGPT API.

## Requirements

- WordPress 5.0+ (tested up to latest).
- PHP 7.4+ recommended.
- [OpenAI](https://platform.openai.com/) account with a valid API key.

## Installation

1. **Download the ZIP** of this repository (or clone).
2. **Upload** it to your WordPress site under `wp-content/plugins/syncweb-content-generator`.
3. In the WordPress admin, go to **Plugins -> Installed Plugins**, find **“Syncweb Content Generator”**, and click **Activate**.
4. Go to **Settings -> Syncweb Content Generator** and enter your **OpenAI API key**.

## Usage

1. Open a post, page, or Elementor editor screen.
2. Look for text fields or textareas; next to them, you’ll see a small **“AI”** button.
3. **Click** the AI button to open a prompt dialog.
4. Type your desired request (e.g., “Generate a short product description for green tea”).
5. The response from ChatGPT appears automatically in your selected field.

## Troubleshooting

- **API Errors (429)**: You may have hit rate limits or exceeded your free trial usage. [Check your OpenAI usage dashboard](https://platform.openai.com/account/usage) or reduce request frequency.
- **No AI Button Appears**:  
  - Make sure you’re on the correct admin screen (e.g., the Elementor editor).  
  - Check that **admin.js** is enqueued (it should be if the plugin is active).  
  - If Elementor loads its fields dynamically, consider using a **MutationObserver** or re-scan the DOM after elements load.
- **Invalid API Key**: Ensure you’ve pasted your API key correctly in the plugin settings.

## Contributing

1. **Fork** the repo and create your feature branch (`git checkout -b feature/awesome-feature`).
2. **Commit** your changes (`git commit -m 'Add an awesome feature'`).
3. **Push** to the branch (`git push origin feature/awesome-feature`).
4. **Open a Pull Request**.

## License

This project is licensed under the [GPL v2 (or later)](https://www.gnu.org/licenses/gpl-2.0.html). Feel free to modify and distribute under the same license.

---

**Enjoy using Syncweb Content Generator?**  
Feel free to report issues, request new features, or contribute improvements. Thank you!
