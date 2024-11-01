import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { MatIconModule } from '@angular/material/icon';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { RecaptchaModule } from 'ng-recaptcha';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavbarComponent, 
            MatIconModule,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            RecaptchaModule
            ],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {
}
