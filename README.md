<a id="readme-top"></a>

<p align="center">
<a href="https://github.com/rodrigogalura/tuner/actions/workflows/pest.yml"><img src="https://img.shields.io/github/actions/workflow/status/rodrigogalura/tuner/pest.yml?label=tests" alt="tests"></a>
<a href="https://packagist.org/packages/rodrigogalura/tuner"><img src="https://img.shields.io/packagist/v/rodrigogalura/tuner" alt="packagist version"></a>
<!-- <a href="https://packagist.org/packages/rodrigogalura/tuner"><img src="https://img.shields.io/packagist/dt/rodrigogalura/tuner" alt="packagist downloads"></a> -->
<a href="https://packagist.org/packages/rodrigogalura/tuner"><img src="https://img.shields.io/github/license/rodrigogalura/tuner" alt="license"></a>
<a href="https://app.codacy.com/gh/rodrigogalura/tuner/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade"><img src="https://app.codacy.com/project/badge/Grade/ed383df93ee2401981f89e9a482c2b1d" alt="code quality"></a>
</p>

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/rodrigogalura/tuner">
    <img src="./art/tuner.png" alt="Logo" width="200">
  </a>

<!-- <h3 align="center">Tuner</h3> -->

  <p align="center">
    A Laravel package to fine-tune your APIs — let clients shape the data with powerful query modifiers.
    <br />
    <a href="https://rodrigogalura.github.io/tuner/docs/installation-guide.html"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <!-- <a href="https://github.com/rodrigogalura/tuner">View Demo</a>
    &middot; -->
    <a href="https://github.com/rodrigogalura/tuner/issues/new?labels=bug&template=bug-report---.md">Report Bug</a>
    &middot;
    <a href="https://github.com/rodrigogalura/tuner/issues/new?labels=enhancement&template=feature-request---.md">Request Feature</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
    </li>
    <li><a href="#main-features">Main Features</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#buy-me-a-coffee">Buy Me a Coffee</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://rodrigogalura.github.io/tuner/docs/installation-guide.html)

This package was born out of real-world needs in my own projects. I created it to cut down on boilerplate and give frontend consumers more control and flexibility. I’m simply sharing the tools that helped me build faster — and I hope they’ll do the same for you.

<!-- Here's a blank template to get started. To avoid retyping too much info, do a search and replace with your text editor for the following: `rodrigogalura`, `tuner`, `rodrigogalura`, `rodrigogalura`, `gmail`, `rodrigogalura3rd`, `Tuner`, `project_description`, `MIT` -->

<!-- <p align="right">(<a href="#readme-top">back to top</a>)</p> -->

---

## Main Features

**Projection**  
Select only the fields you need instead of retrieving every field.  
Available modifiers:  
1. `fields` – Include only the specified fields.  
2. `fields!` – Exclude the specified fields (opposite effect).  

![Projection demo][fields-gif]

---

**Sort**  
Order results in ascending or descending order.  
- Use the `sort` modifier to define one or more sort fields.  

![Sort demo][sort-gif]

---

**Search**  
Filter results based on a search keyword with optional wildcards.  
- Use the `search` modifier.  

Available wildcards:  
1. `*term` – Match at the beginning.  
2. `term*` – Match at the end.  
3. `*term*` – Match anywhere (flexible).  

![Search demo][search-gif]

---

**Filter**  
Go beyond simple search with advanced filtering.  
Available modifiers:  
1. `filter`  
2. `in`  
3. `between`  

_**filter**_  
Supports relational and arithmetic operators:  
- `=` : Equal  
- `>` : Greater than  
- `<` : Less than  
- `>=` : Greater than or equal  
- `<=` : Less than or equal  
- `<>` : Not equal  

![Filter demo][filter-gif]

_**in**_  
Filter results that match any value in a given list.  

![In demo][in-gif]

_**between**_  
Filter results within a range of values (numbers, text, or dates).  

![Between demo][between-gif]

Logical operators are supported for `advanced filtering`:  
- `AND`  
- `OR`  
- `AND!`  
- `OR!`  

![Logical operator demo][logical-and-gif]

---

**Limitation**  
Restrict the number of results returned by specifying a maximum limit.  

![Limit And Offset demo][limit-offset-gif]

---

**Pagination**  
Leverage Laravel’s built-in pagination system for efficient, page-based responses.  

![Pagination demo][pagination-gif]

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- ROADMAP -->
## Roadmap

You can access the Tuner roadmap [here][project_roadmap-url].

---

<!-- ## Contributing

We welcome contributions! See our [CONTRIBUTING.md][contributing-url] for details.  
Please note that we follow a [Code of Conduct][code_of_conduct-url].

---

### Top contributors:

<a href="https://github.com/rodrigogalura/tuner/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=rodrigogalura/tuner" alt="contrib.rocks image" />
</a>

--- -->

<!-- LICENSE -->
## License

The Tuner is open-sourced software licensed under the [MIT license][mit-license-url].


<!-- CONTACT -->
## Contact

If you discover any security vulnerabilities, please contact me. This allows me to address the issue promptly and responsibly.

Rodrigo Galura - rodrigogalura3rd@gmail.com

<!-- ACKNOWLEDGMENTS -->
<!-- ## Acknowledgments

* []()
* []()
* []()

<p align="right">(<a href="#readme-top">back to top</a>)</p> -->

## ☕️ Buy Me a Coffee

If this project helped you or saved you time, consider buying me a coffee. Your support means a lot and helps keep this project active and maintained!

[![Buy Me a Coffee at Ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)][kofi-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- MARKDOWN LINKS, IMAGES, and GIFs -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[product-screenshot]: ./art/image.png
[fields-gif]: ./art/fields.gif
[sort-gif]: ./art/sort.gif
[search-gif]: ./art/search.gif
[filter-gif]: ./art/filter.gif
[in-gif]: ./art/in.gif
[between-gif]: ./art/between.gif
[logical-and-gif]: ./art/logical-and.gif
[limit-offset-gif]: ./art/limit-offset.gif
[pagination-gif]: ./art/pagination.gif

<!-- Account -->
[kofi-url]: https://ko-fi.com/rodrigogalura

<!-- Repo links -->
[pull_request_template-url]: https://github.com/rodrigogalura/tuner/blob/main/.github/PULL_REQUEST_TEMPLATE.md
[bug_report-url]: https://github.com/rodrigogalura/tuner/blob/main/.github/ISSUE_TEMPLATE/bug_report.md
[changelog-url]: https://github.com/rodrigogalura/tuner/blob/main/CHANGELOG.md
[mit-license-url]: https://github.com/rodrigogalura/tuner/blob/main/LICENSE
[project_roadmap-url]: https://github.com/users/rodrigogalura/projects/10/views/5?layout=board
