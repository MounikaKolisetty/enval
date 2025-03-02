import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { ActivatedRoute, Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { UserService } from '../services/user.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-verify-user',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            ScrollButtonComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            CommonModule
  ],
  providers: [UserService],
  templateUrl: './verify-user.component.html',
  styleUrl: './verify-user.component.css'
})
export class VerifyUserComponent {
  verificationStatus: string = 'Verifying...';

  constructor(private route: ActivatedRoute, private userService: UserService, private router: Router) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      const token = params['token'];
      if (token) {
        this.userService.verifyEmail(token).subscribe({
          next: (response) => {
            if (response.success) {
              this.verificationStatus = 'Email verified successfully! Redirecting...';
              setTimeout(() => this.router.navigate(['/login']), 3000);
            } else {
              this.verificationStatus = 'Verification failed: ' + response.message;
            }
          },
          error: () => {
            this.verificationStatus = 'Error verifying email!';
          }
        });
      } else {
        this.verificationStatus = 'Invalid verification link!';
      }
    });
  }

}
