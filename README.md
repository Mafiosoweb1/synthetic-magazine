# 🚀 Synthetic Magazine

![GitHub repo size](https://img.shields.io/github/repo-size/Mafiosoweb1/synthetic-magazine)
![GitHub last commit](https://img.shields.io/github/last-commit/Mafiosoweb1/synthetic-magazine)
![GitHub issues](https://img.shields.io/github/issues/Mafiosoweb1/synthetic-magazine)
![GitHub forks](https://img.shields.io/github/forks/Mafiosoweb1/synthetic-magazine?style=social)
![GitHub stars](https://img.shields.io/github/stars/Mafiosoweb1/synthetic-magazine?style=social)

### ✨ AI-powered Article Generator
Synthetic Magazine is a powerful AI-driven article generator designed for seamless web publishing. This project is based on the [contributte/webapp-skeleton](https://github.com/contributte/webapp-skeleton).

---

## 🌟 Features
✅ **AI-powered content generation** – Create articles effortlessly using artificial intelligence.
🌍 **Seamless web integration** – Easily publish articles online.
⚙️ **Highly configurable** – Customize the generated content to your specific needs.
🚀 **Quick setup** – Get started with minimal effort.

---

## 📥 Installation
1. **Clone the repository**:
   ```sh
   git clone https://github.com/Mafiosoweb1/synthetic-magazine.git
   cd synthetic-magazine
   ```
2. **Install dependencies**:
   ```sh
   composer install
   ```
3. **Configuration**:
	- Edit the `.env` file and update the API key for **ChatGPT**.

4. **Run the project**:
   ```sh
   php -S localhost:8000 -t www
   ```

---

## 🛠 Usage
### 🎯 Console Commands:
🔹 **Fetch articles from WIKIAPI**:
  ```sh
  make console a:c X
  ```
*(X = number of articles to fetch into the database)*

🔹 **Generate and display articles on the web**:
  ```sh
  make console a:g X
  ```
*(X = number of articles to generate and publish immediately on the web)*

---

## 🏗️ Technologies Used
🔹 PHP 8.1+
🔹 `nette/*` packages
🔹 Doctrine ORM via `nettrine/*`
🔹 Symfony components via `contributte/*`

---

## 📜 License
📝 This project is available under the **MIT** license.

---

## 📞 Contact
💬 Have questions or suggestions? Feel free to reach out to [Mafiosoweb1](https://github.com/Mafiosoweb1).
