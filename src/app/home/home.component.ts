import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { MatIconModule } from '@angular/material/icon';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { RecaptchaModule } from 'ng-recaptcha';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavbarComponent, 
            MatIconModule,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            RecaptchaModule,
            CommonModule,
            HttpClientModule,
            ScrollButtonComponent
            ],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {
  isContactVisible: boolean = false; 
  toggleContact() {
    this.isContactVisible = !this.isContactVisible; 
  } 
  closeContact() {
    this.isContactVisible = false; 
  }
}
