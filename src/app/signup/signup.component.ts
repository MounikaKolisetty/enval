import { Component, NgModule } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { UserService } from '../services/user.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-signup',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            HttpClientModule,
            FormsModule 
  ],
  providers: [UserService], // Add UserService as a provider
  templateUrl: './signup.component.html',
  styleUrl: './signup.component.css'
})
export class SignupComponent {
  user: any = { 
    fullName: '', 
    email: '', 
    password: '' 
  };
  passwordFieldType: string = 'password'; 
  constructor(private userService: UserService) {} 
  togglePasswordVisibility() {
     this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'; 
    }
  signUp() { 
    console.log(this.user)

  this.userService.signUp(this.user).subscribe( 
      response => { 
        console.log('User signed up successfully', response); 
      }, 
      error => {
         console.error('Error signing up user', error); 
        } 
      ); 
  }
  
}
