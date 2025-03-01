# iBlog - Personal Blogging Platform

**Team Members:**  
- **Arhaan Khaku**  
- **Bradan Fleming**
- **Germain So**

**University:**  
University of British Columbia (UBC)

**Course:**  
COSC 360: Web Programming

---

## Table of Contents

1. [Project Overview](#project-overview)  
   1.1 [Project Description](#project-description)  
   1.2 [User Types](#user-types)  
   1.3 [Requirements List](#requirements-list)  
   1.4 [Proposed Technologies](#proposed-technologies)  

2. [Design Document](#design-document)  
   2.1 [Wireframes](#wireframes)  
   2.2 [Navigation Structure](#navigation-structure)  

3. [Team Members and Responsibilities](#team-members-and-responsibilities)

---

## Project Overview

### 1.1 Project Description

The iBlog platform is a web application designed to allow users to create, share, and engage with blog content. The platform will support account creation, blog post management, and a commenting system for interaction among users. The platform aims to provide a user-friendly and engaging experience for writers and readers to share insights, thoughts, and discussions on various topics.

The app will offer the following features:
- **Account Creation:** Users can register an account, create a personal profile, and manage account details.
- **Blog Post Creation:** Users can write, edit, and publish blog posts on various topics of interest.
- **Interaction Features:** Users can engage with posts by liking, commenting, and sharing them.
- **Commenting System:** Users can comment on posts to foster discussion and feedback.
- **User Profiles:** Users can view and update their profiles, including published posts and comments.

### 1.2 User Types

There will be three main user roles in the iBlog platform:

- **Unregistered Users:** Can browse the site, search for blog posts, and view content, but cannot interact (post, like, or comment).
- **Registered Users:** Can create and manage their blogs, post content, comment on others' posts, and interact within the community.
- **Admin Users:** Responsible for managing the site, including moderating content, approving/disabling posts, and managing user accounts.

### 1.3 Requirements List

The minimum functional requirements for iBlog include:
- **User Registration:** Users can create accounts by providing a name, email, and profile picture.
- **Blog Management:** Registered users can create and publish blogs, share them with the community, and manage their posts.
- **Profile Management:** Users can view and edit their profiles and manage their content.
- **Search Functionality:** Users can search for blog posts by keyword or topic.
- **Comments and Reviews:** Users can comment on posts and engage in discussions.
- **Admin Controls:** Admins can enable/disable users, moderate posts, and remove inappropriate content.

### 1.4 Proposed Technologies

To build iBlog, the following technologies will be utilized:

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP (for server-side scripting)
- **Database:** MySQL
- **Version Control:** GitHub (for source code management)
- **Deployment:** Deployed to the **cosc360 server** and accessible within the UBC network.

---

## Design Document

### 2.1 Wireframes

Below are the wireframes for key pages within the iBlog platform:

- **Homepage:** Features a search bar, user login, and navigation menu to browse blog posts.
- **Profile Page:** Allows users to view and manage their profiles and blog posts.
- **Write Page:** A page where users can compose and publish blog posts.


### 2.2 Navigation Structure

The platform's navigation will consist of the following sections:

- **Homepage:** Features a search bar, user login, and quick links to other blog posts.
- **Profile Page:** Provides options to view and edit the user profile, and manage content.
- **Admin Panel:** Admin users will have a dedicated panel for managing users, approving blogs, and moderating content.

---

## Team Members and Responsibilities

The project team consists of three members, with each member responsible for specific tasks:

- **Bradan:** Posts, Admin Panel, Database Integration, Skeleton Page Setup, Search Features
- **Arhaan Khaku (You):** Site Design, UI/UX Implementation, Profile Page, Accessibility, User Account & Email Management, Charts
- **Germain:** Comment System, Backend Development, Homepage Design, Form Validation

---

## Getting Started

To set up the project locally, follow these steps:

1. Clone the repository:
   ```bash
   git clone https://github.com/arhaankk/iblog.git
