## 📦 Branching Strategy

This repository follows a structured Git branching model to ensure code quality and a smooth deployment process.

### 🌿 Main Branches

| Branch | Purpose                         |
|--------|----------------------------------|
| `main` | Ongoing development branch       |
| `test` | Internal testing and staging     |
| `prod` | Stable production-ready releases |

### 🧑‍💻 Developer Workflow

1. **Fork the Repository**  
   Developers should **fork** the repository and clone it to their local environment.

2. **Work in Feature Branches**  
   Create feature branches from your forked repo, based on the `main` branch.

   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Pull Request Rules**
   - ✅ PRs should be made **only to the `main` branch**.
   - 🚨 Only **urgent hotfixes** may be PR’d directly to the `test` branch (with approval).
   - ❌ No one should push directly to `main`, `test`, or `prod`.

4. **Merging Flow**
   - `main` → `test` (after dev review or sprint completion)
   - `test` → `prod` (after full testing and approval)

---

## 🧪 Testing & Deployment

CI/CD pipelines handle deployments from:
- `test` → Staging Environment
- `prod` → Production Environment

---


